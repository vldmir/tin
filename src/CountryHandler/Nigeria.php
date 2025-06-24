<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Nigeria TIN validation.
 * Supports TIN (Tax Identification Number) - 10 digits JTB format.
 */
final class Nigeria extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'NG';

    /**
     * @var int
     */
    public const LENGTH = 10;

    /**
     * @var string
     */
    public const MASK = '9999999999';

    /**
     * TIN Pattern: 10 digits (can be formatted as 9999999999).
     *
     * @var string
     */
    public const PATTERN = '^\d{10}$';

    /**
     * Returns a placeholder example of a Nigerian TIN.
     *
     * @return string The placeholder TIN '1234567890'.
     */
    public function getPlaceholder(): string
    {
        return '1234567890';
    }

    /**
     * Returns an array of supported Nigerian TIN types with their metadata.
     *
     * @return array An associative array describing the supported TIN types for Nigeria.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'TIN',
                'name' => 'Nigerian TIN',
                'description' => 'Nigerian Tax Identification Number',
            ],
        ];
    }

    /**
     * Checks if the provided TIN matches the required 10-digit numeric pattern for Nigeria.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN matches the 10-digit pattern, false otherwise.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Determines if the provided TIN satisfies Nigerian-specific validity rules.
     *
     * Returns false if the TIN consists entirely of zeros, all digits are identical, or the first digit is zero; otherwise, returns true.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN passes all rule-based checks; false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        // Check if all digits are zeros (invalid)
        if (preg_match('/^0+$/', $tin)) {
            return false;
        }

        // Check if all digits are the same (invalid)
        if (preg_match('/^(\d)\1{9}$/', $tin)) {
            return false;
        }

        // First digit should not be 0
        if ('0' === $tin[0]) {
            return false;
        }

        return true;
    }
}
