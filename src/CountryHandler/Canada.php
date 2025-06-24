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
     * Formats a Canadian TIN input as either a SIN or BN, applying standard presentation rules.
     *
     * Normalizes the input by removing non-alphanumeric characters and converting to uppercase. Formats extended Business Numbers (BN) as "123456789 RC0001" and 9-digit numbers as Social Insurance Numbers (SIN) in the "999-999-999" format. Returns the normalized input if it does not match these patterns.
     *
     * @param string $input The raw TIN input to format.
     * @return string The formatted TIN string.
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
     * Returns the default input mask for Canadian TINs.
     *
     * The mask '999-999-999' is used for Social Insurance Numbers (SIN).
     *
     * @return string The input mask for formatting Canadian TINs.
     */
    public function getInputMask(): string
    {
        // Default to SIN format
        return '999-999-999';
    }

    /**
     * Returns the placeholder string for Canadian TIN input fields.
     *
     * @return string The placeholder value '123-456-789' representing the SIN format.
     */
    public function getPlaceholder(): string
    {
        return '123-456-789';
    }

    /**
     * Returns an array describing the supported Canadian TIN types.
     *
     * Each entry includes the code, name, and description for either the Social Insurance Number (SIN) or Business Number (BN).
     *
     * @return array Supported TIN types for Canada.
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
     * Determines whether the provided Canadian TIN is a Social Insurance Number (SIN) or Business Number (BN).
     *
     * Normalizes the input and checks if it matches the format and validation rules for SIN or BN, including extended BN with program account suffix.
     *
     * @param string $tin The input Tax Identification Number to identify.
     * @return array|null The TIN type metadata if identified, or null if the input does not match any supported type.
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

    /**
     * Checks if the provided TIN has a valid length for a Canadian SIN or BN.
     *
     * Returns true if the normalized TIN is exactly 9 digits (SIN) or 9 digits followed by an optional 2-letter and 4-digit suffix (BN).
     *
     * @param string $tin The input Tax Identification Number.
     * @return bool True if the TIN length is valid for SIN or BN, false otherwise.
     */
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

    /**
     * Checks if the provided TIN matches the Canadian SIN or BN pattern.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN matches the expected pattern; false otherwise.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Determines if the provided TIN satisfies the rule-based validation for Canadian SIN or BN formats.
     *
     * Returns true if the input is a valid SIN (using Luhn algorithm and prefix rules), a valid 9-digit BN, or a valid extended BN (9 digits plus program account suffix). Returns false otherwise.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN passes rule-based validation for SIN or BN; false otherwise.
     */
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
     * Checks if the provided Business Number (BN) is a valid 9-digit number and not all zeros.
     *
     * @param string $bn The Business Number to validate.
     * @return bool True if the BN is exactly 9 digits and not all zeros; false otherwise.
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
     * Determines if a Canadian Social Insurance Number (SIN) is valid.
     *
     * Validates that the SIN does not start with 0, 8, or 9, and that it passes the Luhn checksum algorithm.
     *
     * @param string $sin The SIN to validate, consisting of 9 digits.
     * @return bool True if the SIN is valid, false otherwise.
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
