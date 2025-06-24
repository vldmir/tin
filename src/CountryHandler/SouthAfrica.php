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
    public const PATTERN = '^[0-3,9]\d{9}$';

    /**
     * Returns a placeholder string representing the format of a South African Income Tax Reference Number.
     *
     * @return string The placeholder value '0123456789'.
     */
    public function getPlaceholder(): string
    {
        return '0123456789';
    }

    /**
     * Returns an array of supported South African TIN types with their codes, names, and descriptions.
     *
     * @return array An associative array describing the supported TIN types.
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

    /**
     * Checks if the provided TIN matches the required South African format pattern.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN matches the pattern; otherwise, false.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates a South African TIN against rule-based criteria.
     *
     * Checks that the TIN starts with an allowed digit, does not consist of identical digits, and passes the Luhn checksum.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN meets all rule-based validation criteria, false otherwise.
     */
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
     * Validates a numeric string using the Luhn algorithm.
     *
     * Applies the Luhn checksum to determine if the input number is valid according to the algorithm.
     *
     * @param string $number The numeric string to validate.
     * @return bool True if the number passes the Luhn check, false otherwise.
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
