<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Saudi Arabia TIN validation.
 * Supports Tax Identification Number (VAT registration number) - 15 digits starting with 3.
 */
final class SaudiArabia extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'SA';

    /**
     * @var int
     */
    public const LENGTH = 15;

    /**
     * @var string
     */
    public const MASK = '999999999999999';

    /**
     * TIN Pattern: 15 digits starting with 3.
     *
     * @var string
     */
    public const PATTERN = '^3\d{14}$';

    /**
     * Returns a sample Saudi Arabia VAT registration number as a placeholder.
     *
     * @return string Example TIN in the correct format.
     */
    public function getPlaceholder(): string
    {
        return '300123456789123';
    }

    /**
     * Returns an array of supported TIN types for Saudi Arabia.
     *
     * @return array An array containing information about the Saudi VAT Number TIN type.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'VAT',
                'name' => 'Saudi VAT Number',
                'description' => 'Saudi Arabia VAT Registration Number',
            ],
        ];
    }

    /**
     * Checks if the provided TIN matches the required Saudi Arabia VAT number pattern.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN matches the pattern; otherwise, false.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates the Saudi Arabia VAT TIN against country-specific rules.
     *
     * Checks that the TIN starts with '3', is not composed of identical digits, and that the check digit (15th digit) is valid according to the Saudi VAT algorithm.
     *
     * @param string $tin The TIN to validate.
     * @return bool True if the TIN passes all rule-based validations, false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        // Must start with 3
        if ('3' !== $tin[0]) {
            return false;
        }

        // Check if all digits are the same (invalid)
        if (preg_match('/^(\d)\1{14}$/', $tin)) {
            return false;
        }

        // The 15th digit is a check digit
        return $this->validateCheckDigit($tin);
    }

    /**
     * Validates the check digit of a Saudi Arabia VAT number using the modulo 11 algorithm.
     *
     * Applies predefined weights to the first 14 digits, computes the weighted sum, and derives the check digit according to Saudi VAT rules. Returns true if the calculated check digit matches the 15th digit of the TIN.
     *
     * @param string $tin The 15-digit Saudi VAT number to validate.
     * @return bool True if the check digit is valid, false otherwise.
     */
    private function validateCheckDigit(string $tin): bool
    {
        // Weights for positions 1-14 (right to left)
        $weights = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
        $sum = 0;

        // Calculate weighted sum for first 14 digits
        for ($i = 0; 14 > $i; ++$i) {
            $sum += ((int) $tin[$i]) * $weights[13 - $i];
        }

        $remainder = $sum % 11;
        $checkDigit = 11 - $remainder;

        if (10 === $checkDigit) {
            $checkDigit = 0;
        } elseif (11 === $checkDigit) {
            $checkDigit = 1;
        }

        return (int) $tin[14] === $checkDigit;
    }
}
