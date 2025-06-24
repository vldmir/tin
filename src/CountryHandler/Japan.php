<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * Japan TIN validation.
 * Supports My Number (12 digits) and Corporate Number (13 digits).
 */
final class Japan extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'JP';

    /**
     * @var int
     */
    public const LENGTH = 13; // Maximum length

    /**
     * @var string
     */
    public const MASK = '9999999999999'; // Default to corporate

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^\d{12,13}$';

    /**
     * Returns the input mask string for Japanese My Number TINs (12 digits).
     *
     * This mask is used to guide user input for individual TINs in Japan.
     *
     * @return string The input mask for a 12-digit My Number.
     */
    public function getInputMask(): string
    {
        // Default to My Number format
        return '999999999999';
    }

    /**
     * Returns a placeholder example for a Japanese My Number TIN.
     *
     * @return string Example placeholder for a 12-digit My Number.
     */
    public function getPlaceholder(): string
    {
        return '123456789012';
    }

    /**
     * Returns an array of supported Japanese TIN types with their codes, names, and descriptions.
     *
     * @return array An associative array describing My Number and Corporate Number TIN types.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'MYNUMBER',
                'name' => 'My Number',
                'description' => 'Individual identification number',
            ],
            2 => [
                'code' => 'CORPORATE',
                'name' => 'Corporate Number',
                'description' => 'Corporate identification number',
            ],
        ];
    }

    /**
     * Determines the type of a Japanese TIN (My Number or Corporate Number) based on its length and validity.
     *
     * Normalizes the input TIN, validates it according to the rules for My Number (12 digits) or Corporate Number (13 digits), and returns the corresponding TIN type array if valid.
     *
     * @param string $tin The Japanese TIN to identify.
     * @return array|null The TIN type information if valid, or null if the TIN is invalid or unrecognized.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);

        if (strlen($normalizedTin) === 12 && $this->isValidMyNumber($normalizedTin)) {
            return $this->getTinTypes()[1]; // My Number
        }

        if (strlen($normalizedTin) === 13 && $this->isValidCorporateNumber($normalizedTin)) {
            return $this->getTinTypes()[2]; // Corporate Number
        }

        return null;
    }

    /**
     * Checks if the TIN has a valid length for Japanese My Number (12 digits) or Corporate Number (13 digits).
     *
     * @param string $tin The Tax Identification Number to check.
     * @return bool True if the length is 12 or 13 digits, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        $length = strlen($tin);

        return 12 === $length || 13 === $length;
    }

    /**
     * Checks if the provided TIN matches the required numeric pattern for Japanese TINs.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN matches the pattern for either My Number or Corporate Number; otherwise, false.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates the TIN using the appropriate algorithm based on its length.
     *
     * Applies the My Number validation for 12-digit TINs and the Corporate Number validation for 13-digit TINs.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN passes the relevant validation rule, false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $length = strlen($tin);

        if (12 === $length) {
            return $this->isValidMyNumber($tin);
        }

        if (13 === $length) {
            return $this->isValidCorporateNumber($tin);
        }

        return false;
    }

    /**
     * Validates a 13-digit Japanese Corporate Number using the modulus 9 check digit algorithm.
     *
     * The function checks that the first digit is non-zero, rejects numbers with all identical digits, and verifies the check digit according to the official algorithm.
     *
     * @param string $number The 13-digit Corporate Number to validate.
     * @return bool True if the number is valid, false otherwise.
     */
    private function isValidCorporateNumber(string $number): bool
    {
        // First digit should be 1-9 (organization type)
        if ('0' === $number[0]) {
            return false;
        }

        // Check if all digits are the same (invalid)
        if (preg_match('/^(\d)\1{12}$/', $number)) {
            return false;
        }

        // Calculate check digit
        $sum = 0;

        for ($i = 0; 12 > $i; ++$i) {
            $weight = ($i % 2 === 0) ? 1 : 2;
            $sum += ((int) $number[11 - $i]) * $weight;
        }

        $checkDigit = 9 - ($sum % 9);

        return (int) $number[12] === $checkDigit;
    }

    /**
     * Validates a 12-digit Japanese My Number (individual TIN) using its check digit algorithm.
     *
     * Rejects numbers with all identical digits and verifies the check digit according to the official My Number specification.
     *
     * @param string $number The 12-digit My Number to validate.
     * @return bool True if the number is a valid My Number, false otherwise.
     */
    private function isValidMyNumber(string $number): bool
    {
        // Check if all digits are the same (invalid)
        if (preg_match('/^(\d)\1{11}$/', $number)) {
            return false;
        }

        // Calculate check digit
        $sum = 0;

        for ($i = 0; 11 > $i; ++$i) {
            $p = 11 - $i;
            $q = (6 >= $p) ? $p + 1 : $p - 5;
            $sum += ((int) $number[$i]) * $q;
        }

        $remainder = $sum % 11;
        $checkDigit = (1 >= $remainder) ? 0 : 11 - $remainder;

        return (int) $number[11] === $checkDigit;
    }
}
