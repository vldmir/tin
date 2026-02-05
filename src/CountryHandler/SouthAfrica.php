<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function in_array;
use function strlen;

/**
 * South Africa TIN validation.
 * Supports Income Tax Reference Number - 10 digits starting with 0, 1, 2, 3, or 9.
 */
final class SouthAfrica extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'ZA';

    /**
     * @var int
     */
    public const LENGTH = 10;

    /**
     * @var string
     */
    public const MASK = '9999999999';

    /**
     * TIN Pattern: 10 digits starting with 0, 1, 2, 3, or 9.
     *
     * @var string
     */
    public const PATTERN = '^[0-39]\d{9}$';

    /**
     * Get placeholder text.
     */
    public function getPlaceholder(): string
    {
        return '0123456789';
    }

    /**
     * Get all TIN types supported by South Africa.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'ITR',
                'name' => 'South African ITR',
                'description' => 'South African Income Tax Reference Number',
            ],
        ];
    }

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    protected function hasValidRule(string $tin): bool
    {
        // First digit must be 0, 1, 2, 3, or 9
        if (!in_array($tin[0], ['0', '1', '2', '3', '9'], true)) {
            return false;
        }

        // Check if all digits are the same (invalid)
        if (preg_match('/^(\d)\1{9}$/', $tin)) {
            return false;
        }

        // Validate using Luhn algorithm
        return $this->validateLuhn($tin);
    }

    /**
     * Validate using Luhn algorithm.
     */
    private function validateLuhn(string $number): bool
    {
        $sum = 0;
        $alternate = false;

        // Process from right to left
        for ($i = strlen($number) - 1; 0 <= $i; --$i) {
            $digit = (int) $number[$i];

            if ($alternate) {
                $digit *= 2;

                if (9 < $digit) {
                    $digit = ($digit % 10) + 1;
                }
            }

            $sum += $digit;
            $alternate = !$alternate;
        }

        return 0 === ($sum % 10);
    }
}
