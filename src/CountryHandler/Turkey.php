<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * Turkey TIN validation.
 * Supports T.C. Kimlik No (11 digits personal) and Vergi Kimlik No (10 digits business).
 */
final class Turkey extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'TR';

    /**
     * @var int
     */
    public const LENGTH = 11; // Maximum length

    /**
     * @var string
     */
    public const MASK = '99999999999'; // Default to personal

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^\d{10,11}$';

    /**
     * Returns a sample Turkish TIN as a placeholder string.
     *
     * This placeholder represents the format of a valid Turkish personal identification number (T.C. Kimlik No).
     *
     * @return string Example TIN placeholder.
     */
    public function getPlaceholder(): string
    {
        return '12345678901';
    }

    /**
     * Returns an array of supported Turkish TIN types, including personal and business identification numbers.
     *
     * @return array An associative array describing each TIN type with code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'TCKN',
                'name' => 'T.C. Kimlik No',
                'description' => 'Turkish national identification number',
            ],
            2 => [
                'code' => 'VKN',
                'name' => 'Vergi Kimlik No',
                'description' => 'Turkish tax identification number for businesses',
            ],
        ];
    }

    /**
     * Determines the type of a Turkish TIN (Tax Identification Number) based on its format and validity.
     *
     * Returns an array describing the TIN type if the input is a valid Turkish personal or business TIN, or null if unrecognized or invalid.
     *
     * @param string $tin The Turkish TIN to evaluate.
     * @return array|null The TIN type metadata if valid, or null if the TIN is invalid or unrecognized.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);

        if (strlen($normalizedTin) === 11 && $this->isValidPersonalID($normalizedTin)) {
            return $this->getTinTypes()[1]; // T.C. Kimlik No
        }

        if (strlen($normalizedTin) === 10 && $this->isValidBusinessID($normalizedTin)) {
            return $this->getTinTypes()[2]; // Vergi Kimlik No
        }

        return null;
    }

    /**
     * Checks if the TIN has a valid length of either 10 or 11 digits.
     *
     * @param string $tin The Tax Identification Number to check.
     * @return bool True if the TIN length is valid, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        $length = strlen($tin);

        return 10 === $length || 11 === $length;
    }

    /**
     * Checks if the provided TIN matches the required Turkish TIN pattern.
     *
     * @param string $tin The tax identification number to validate.
     * @return bool True if the TIN matches the pattern; false otherwise.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates a Turkish TIN using the appropriate checksum rule based on its length.
     *
     * Applies the personal ID validation algorithm for 11-digit TINs and the business tax ID validation algorithm for 10-digit TINs. Returns false for any other length.
     *
     * @param string $tin The Turkish TIN to validate.
     * @return bool True if the TIN passes the relevant checksum validation; false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $length = strlen($tin);

        if (11 === $length) {
            return $this->isValidPersonalID($tin);
        }

        if (10 === $length) {
            return $this->isValidBusinessID($tin);
        }

        return false;
    }

    /**
     * Validates a 10-digit Turkish business tax identification number (Vergi Kimlik No) using its checksum algorithm.
     *
     * The function returns true if the provided ID passes the checksum validation and is not all zeros; otherwise, it returns false.
     *
     * @param string $id The 10-digit business tax identification number to validate.
     * @return bool True if the ID is valid, false otherwise.
     */
    private function isValidBusinessID(string $id): bool
    {
        // Cannot be all zeros
        if ('0000000000' === $id) {
            return false;
        }

        // Apply checksum algorithm
        $v = [];

        for ($i = 0; 9 > $i; ++$i) {
            $v[$i + 1] = (int) $id[$i];
        }

        $lastDigit = (int) $id[9];

        for ($i = 1; 9 >= $i; ++$i) {
            $v[$i] = ($v[$i] + $i) % 10;
        }

        for ($i = 1; 9 >= $i; ++$i) {
            $v[$i] = ($v[$i] * 2 ** $i) % 9;

            if (0 === $v[$i]) {
                $v[$i] = 9;
            }
        }

        $sum = 0;

        for ($i = 1; 9 >= $i; ++$i) {
            $sum += $v[$i];
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $checkDigit === $lastDigit;
    }

    /**
     * Validates an 11-digit Turkish personal identification number (T.C. Kimlik No) using checksum algorithms.
     *
     * The function checks that the first digit is not zero, the number is not composed of identical digits, and both the 10th and 11th digits are valid according to official checksum rules.
     *
     * @param string $id The 11-digit personal identification number to validate.
     * @return bool True if the ID is valid; false otherwise.
     */
    private function isValidPersonalID(string $id): bool
    {
        // First digit cannot be 0
        if ('0' === $id[0]) {
            return false;
        }

        // Cannot be all same digits
        if (preg_match('/^(\d)\1{10}$/', $id)) {
            return false;
        }

        // Calculate first check digit (10th digit)
        $oddSum = 0;
        $evenSum = 0;

        for ($i = 0; 9 > $i; ++$i) {
            if ($i % 2 === 0) {
                $oddSum += (int) $id[$i];
            } else {
                $evenSum += (int) $id[$i];
            }
        }

        $checkDigit1 = ((7 * $oddSum) - $evenSum) % 10;

        if (0 > $checkDigit1) {
            $checkDigit1 += 10;
        }

        if ((int) $id[9] !== $checkDigit1) {
            return false;
        }

        // Calculate second check digit (11th digit)
        $totalSum = 0;

        for ($i = 0; 10 > $i; ++$i) {
            $totalSum += (int) $id[$i];
        }

        $checkDigit2 = $totalSum % 10;

        return (int) $id[10] === $checkDigit2;
    }
}
