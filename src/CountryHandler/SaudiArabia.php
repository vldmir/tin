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
     * Get placeholder text.
     */
    public function getPlaceholder(): string
    {
        return '300123456789123';
    }

    /**
     * Get all TIN types supported by Saudi Arabia.
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

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

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
     * Validate check digit using modulo 11 algorithm.
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
