<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function in_array;
use function strlen;

/**
 * Argentina TIN validation.
 * Supports CUIT (Clave Única de Identificación Tributaria).
 */
final class Argentina extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'AR';

    /**
     * @var int
     */
    public const LENGTH = 11;

    /**
     * @var string
     */
    public const MASK = '99-99999999-9';

    /**
     * CUIT Pattern: 99-99999999-9.
     *
     * @var string
     */
    public const PATTERN = '^\d{2}-?\d{8}-?\d{1}$';

    /**
     * Formats a string into the standard CUIT pattern (99-99999999-9).
     *
     * Removes all non-digit characters from the input and applies the CUIT mask if enough digits are present. Returns an empty string if the input contains no digits.
     *
     * @param string $input The input string to format as a CUIT.
     * @return string The formatted CUIT string or an empty string if input is invalid.
     */
    public function formatInput(string $input): string
    {
        $normalized = preg_replace('/[^0-9]/', '', $input);

        if ('' === $normalized) {
            return '';
        }

        // Format as: 99-99999999-9
        if (strlen($normalized) >= 2) {
            $result = substr($normalized, 0, 2);

            if (strlen($normalized) > 2) {
                $result .= '-' . substr($normalized, 2, 8);

                if (strlen($normalized) > 10) {
                    $result .= '-' . substr($normalized, 10, 1);
                }
            }

            return $result;
        }

        return $normalized;
    }

    /**
     * Returns a sample CUIT placeholder in the standard Argentina TIN format.
     *
     * @return string Example CUIT formatted as '20-12345678-9'.
     */
    public function getPlaceholder(): string
    {
        return '20-12345678-9';
    }

    /**
     * Returns an array describing the supported Tax Identification Number (TIN) types for Argentina.
     *
     * @return array An array containing metadata for each supported TIN type, including code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'CUIT',
                'name' => 'Clave Única de Identificación Tributaria',
                'description' => 'Unique Tax Identification Key for individuals and companies',
            ],
        ];
    }

    /**
     * Checks if the provided TIN has a valid length after removing non-digit characters.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the normalized TIN is exactly 11 digits long, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

        return strlen($normalizedTin) === self::LENGTH;
    }

    /**
     * Checks if the provided TIN matches the CUIT format pattern for Argentina.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN matches the CUIT regex pattern; otherwise, false.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates the CUIT by checking its type prefix and verifying the checksum.
     *
     * The method normalizes the input, ensures it is 11 digits, checks that the type prefix is valid for Argentina's CUIT, and confirms the check digit using the CUIT checksum algorithm.
     *
     * @param string $tin The CUIT to validate.
     * @return bool True if the CUIT passes type and checksum validation; false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

        if (strlen($normalizedTin) !== 11) {
            return false;
        }

        // Extract parts
        $type = substr($normalizedTin, 0, 2);
        $number = substr($normalizedTin, 2, 8);
        $checkDigit = (int) substr($normalizedTin, 10, 1);

        // Validate type
        if (!$this->isValidType($type)) {
            return false;
        }

        // Validate checksum
        return $this->validateChecksum($normalizedTin);
    }

    /**
     * Checks if the provided CUIT type prefix is valid for individuals or companies in Argentina.
     *
     * @param string $type The two-digit CUIT type prefix.
     * @return bool True if the prefix is valid, false otherwise.
     */
    private function isValidType(string $type): bool
    {
        // Valid prefixes:
        // 20, 23, 24, 27: Individuals
        // 30, 33, 34: Companies
        $validTypes = ['20', '23', '24', '27', '30', '33', '34'];

        return in_array($type, $validTypes, true);
    }

    /**
     * Validates the CUIT checksum using the modulo 11 algorithm.
     *
     * Calculates the check digit for the given CUIT and verifies it matches the last digit.
     * Returns false for special cases where the check digit is 10, as these require additional handling not implemented here.
     *
     * @param string $cuit The normalized 11-digit CUIT string.
     * @return bool True if the checksum is valid, false otherwise.
     */
    private function validateChecksum(string $cuit): bool
    {
        $weights = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        for ($i = 0; 10 > $i; ++$i) {
            $sum += ((int) $cuit[$i]) * $weights[$i];
        }

        $remainder = $sum % 11;
        $checkDigit = 11 - $remainder;

        // Special cases
        if (11 === $checkDigit) {
            $checkDigit = 0;
        } elseif (10 === $checkDigit) {
            // For type 20 (male), it should be 23 with check digit 9
            // For type 27 (female), it should be 23 with check digit 4
            // This is a simplification - in practice it's more complex
            return false;
        }

        return (int) $cuit[10] === $checkDigit;
    }
}
