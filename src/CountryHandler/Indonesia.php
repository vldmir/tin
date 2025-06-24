<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * Indonesia TIN validation.
 * Supports NPWP (Nomor Pokok Wajib Pajak) - 16 digits.
 */
final class Indonesia extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'ID';

    /**
     * @var int
     */
    public const LENGTH = 16;

    /**
     * @var string
     */
    public const MASK = '99.999.999.9-999.999';

    /**
     * NPWP Pattern: 16 digits (can be formatted as 99.999.999.9-999.999).
     *
     * @var string
     */
    public const PATTERN = '^\d{2}\.?\d{3}\.?\d{3}\.?\d{1}-?\d{3}\.?\d{3}$|^\d{16}$';

    /**
     * Formats a string as an Indonesian NPWP (Tax Identification Number).
     *
     * Removes all non-digit characters from the input and applies the standard NPWP format: 99.999.999.9-999.999. Returns an empty string if the input contains no digits.
     *
     * @param string $input The input string to format.
     * @return string The formatted NPWP string, or an empty string if input is empty after normalization.
     */
    public function formatInput(string $input): string
    {
        $normalized = preg_replace('/[^0-9]/', '', $input);

        if ('' === $normalized) {
            return '';
        }

        // Format as: 99.999.999.9-999.999
        $result = '';

        for ($i = 0; strlen($normalized) > $i && 16 > $i; ++$i) {
            if (2 === $i || 5 === $i || 8 === $i || 12 === $i) {
                $result .= '.';
            } elseif (9 === $i) {
                $result .= '-';
            }
            $result .= $normalized[$i];
        }

        return $result;
    }

    /**
     * Returns a sample formatted Indonesian NPWP as a placeholder string.
     *
     * @return string Example NPWP in the standard format.
     */
    public function getPlaceholder(): string
    {
        return '01.234.567.8-901.234';
    }

    /**
     * Returns metadata for all supported Indonesian TIN types.
     *
     * @return array An array containing information about the NPWP TIN type, including its code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'NPWP',
                'name' => 'Indonesian NPWP',
                'description' => 'Indonesian Tax Registration Number (Nomor Pokok Wajib Pajak)',
            ],
        ];
    }

    /**
     * Checks if the normalized TIN contains exactly 16 digits.
     *
     * @param string $tin The input Tax Identification Number.
     * @return bool True if the TIN has 16 digits after removing non-digit characters; otherwise, false.
     */
    protected function hasValidLength(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

        return strlen($normalizedTin) === self::LENGTH;
    }

    /**
     * Checks if the provided TIN matches the Indonesian NPWP format pattern.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN matches the NPWP pattern; otherwise, false.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Applies semantic validation rules to an Indonesian NPWP TIN.
     *
     * Checks that the TIN is 16 digits, not all zeros, and that the tax office code (first two digits) is not '00'.
     *
     * @param string $tin The TIN to validate.
     * @return bool True if the TIN passes all rule-based checks, false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

        if (strlen($normalizedTin) !== 16) {
            return false;
        }

        // Check if all digits are zeros (invalid)
        if (preg_match('/^0+$/', $normalizedTin)) {
            return false;
        }

        // First 2 digits are the tax office code
        $taxOfficeCode = substr($normalizedTin, 0, 2);

        // Tax office code should not be 00
        if ('00' === $taxOfficeCode) {
            return false;
        }

        // Digits 10-12 are the branch code (KPP)
        $branchCode = substr($normalizedTin, 9, 3);

        // Branch code 000 is for head office, others for branches
        // Both are valid, so no additional check needed

        return true;
    }
}
