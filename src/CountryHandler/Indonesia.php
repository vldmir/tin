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
     * Format input according to NPWP format.
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
     * Get placeholder text.
     */
    public function getPlaceholder(): string
    {
        return '01.234.567.8-901.234';
    }

    /**
     * Get all TIN types supported by Indonesia.
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

    protected function hasValidLength(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

        return strlen($normalizedTin) === self::LENGTH;
    }

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

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
