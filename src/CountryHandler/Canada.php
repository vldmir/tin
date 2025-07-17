<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function in_array;
use function strlen;

/**
 * Canada TIN validation.
 * Supports SIN (Social Insurance Number) with Luhn checksum and BN (Business Number).
 */
final class Canada extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'CA';

    /**
     * @var int
     */
    public const LENGTH = 9; // Base length for both SIN and BN

    /**
     * @var string
     */
    public const MASK = '999-999-999'; // Default to SIN

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^(\d{3}-?\d{3}-?\d{3}|\d{9}([A-Z]{2}\d{4})?)$';

    /**
     * BN Pattern: 999999999 (9 digits) + optional program account (e.g., RC0001).
     *
     * @var string
     */
    public const PATTERN_BN = '^\d{9}([A-Z]{2}\d{4})?$';

    /**
     * SIN Pattern: 999-999-999 (9 digits).
     *
     * @var string
     */
    public const PATTERN_SIN = '^\d{3}-?\d{3}-?\d{3}$';

    /**
     * Format input according to TIN type.
     */
    public function formatInput(string $input): string
    {
        $normalized = preg_replace('/[^0-9A-Z]/', '', strtoupper($input));

        if ('' === $normalized) {
            return '';
        }

        // Check if it's extended BN format (with program account)
        if (preg_match('/^\d{9}[A-Z]{2}\d{4}$/', $normalized)) {
            // Format as BN with program account: 123456789 RC0001
            return substr($normalized, 0, 9) . ' ' . substr($normalized, 9);
        }

        // For 9-digit numbers, default to SIN format
        if (preg_match('/^\d+$/', $normalized) && strlen($normalized) <= 9) {
            // Format as SIN: 999-999-999
            $result = '';

            for ($i = 0; strlen($normalized) > $i && 9 > $i; ++$i) {
                if (3 === $i || 6 === $i) {
                    $result .= '-';
                }
                $result .= $normalized[$i];
            }

            return $result;
        }

        return $normalized;
    }

    /**
     * Get input mask based on the TIN type.
     */
    public function getInputMask(): string
    {
        // Default to SIN format
        return '999-999-999';
    }

    /**
     * Get placeholder based on the TIN type.
     */
    public function getPlaceholder(): string
    {
        return '123-456-789';
    }

    /**
     * Get all TIN types supported by Canada.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'SIN',
                'name' => 'Social Insurance Number',
                'description' => 'Social Insurance Number for individuals',
            ],
            2 => [
                'code' => 'BN',
                'name' => 'Business Number',
                'description' => 'Business Number for corporations and businesses',
            ],
        ];
    }

    /**
     * Identify the TIN type for a given Canadian TIN.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = preg_replace('/[^0-9A-Z]/', '', $this->normalizeTin($tin));

        // Check if it's 9 digits (could be SIN or BN)
        if (preg_match('/^\d{9}$/', $normalizedTin)) {
            // Try SIN validation first
            if ($this->isValidSIN($normalizedTin)) {
                return $this->getTinTypes()[1]; // SIN
            }

            // Otherwise, check if it's BN
            if ($this->isValidBN($normalizedTin)) {
                return $this->getTinTypes()[2]; // BN
            }
        }

        // Check if it's extended BN format
        if (preg_match('/^\d{9}[A-Z]{2}\d{4}$/', $normalizedTin)) {
            if ($this->isValidBN(substr($normalizedTin, 0, 9))) {
                return $this->getTinTypes()[2]; // BN
            }
        }

        return null;
    }

    protected function hasValidLength(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9A-Z]/', '', $tin);

        // SIN is always 9 digits
        if (preg_match('/^\d{9}$/', $normalizedTin)) {
            return true;
        }

        // BN can be 9 digits or 9 digits + 2 letters + 4 digits
        if (preg_match('/^\d{9}([A-Z]{2}\d{4})?$/', $normalizedTin)) {
            return true;
        }

        return false;
    }

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    protected function hasValidRule(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9A-Z]/', '', $tin);

        // Check if it's SIN format (9 digits only)
        if (preg_match('/^\d{9}$/', $normalizedTin)) {
            // Could be either SIN or BN base number
            // Try SIN validation first (more restrictive)
            if ($this->isValidSIN($normalizedTin)) {
                return true;
            }

            // If not valid SIN, check if it's a valid BN
            return $this->isValidBN($normalizedTin);
        }

        // Check if it's extended BN format
        if (preg_match('/^\d{9}[A-Z]{2}\d{4}$/', $normalizedTin)) {
            return $this->isValidBN(substr($normalizedTin, 0, 9));
        }

        return false;
    }

    /**
     * Validate Business Number.
     */
    private function isValidBN(string $bn): bool
    {
        // BN must be 9 digits
        if (!preg_match('/^\d{9}$/', $bn)) {
            return false;
        }

        // First 9 digits should not be all zeros
        if ('000000000' === $bn) {
            return false;
        }

        // Basic validation passed
        // Note: Real BN validation would require checking against CRA database
        return true;
    }

    /**
     * Validate SIN using Luhn algorithm.
     */
    private function isValidSIN(string $sin): bool
    {
        // SIN cannot start with 0, 8, or 9
        if (in_array($sin[0], ['0', '8', '9'], true)) {
            return false;
        }

        // Apply Luhn algorithm
        $sum = 0;
        $alternate = false;

        // Process from right to left
        for ($i = strlen($sin) - 1; 0 <= $i; --$i) {
            $digit = (int) $sin[$i];

            if ($alternate) {
                $digit *= 2;

                if (9 < $digit) {
                    $digit = ($digit % 10) + 1; // Same as subtracting 9
                }
            }

            $sum += $digit;
            $alternate = !$alternate;
        }

        return 0 === ($sum % 10);
    }
}
