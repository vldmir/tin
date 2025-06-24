<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function in_array;
use function strlen;

/**
 * Australia TIN validation.
 * Supports TFN (Tax File Number) and ABN (Australian Business Number) with checksum.
 */
final class Australia extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'AU';

    /**
     * @var int
     */
    public const LENGTH = 11; // Maximum length (ABN)

    /**
     * @var string
     */
    public const MASK = '99 999 999 999'; // Default to ABN

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^(\d{8,9}|\d{2}\s?\d{3}\s?\d{3}\s?\d{3})$';

    /**
     * ABN Pattern: 11 digits (can be formatted as 99 999 999 999).
     *
     * @var string
     */
    public const PATTERN_ABN = '^\d{2}\s?\d{3}\s?\d{3}\s?\d{3}$';

    /**
     * TFN Pattern: 8-9 digits.
     *
     * @var string
     */
    public const PATTERN_TFN = '^\d{8,9}$';

    /**
     * Formats an Australian TIN input as either an ABN with spaces or a plain TFN.
     *
     * Normalizes the input by removing non-digit characters. If the input has 10 or more digits, it is formatted as an ABN (`99 999 999 999`). Shorter inputs (TFN) are returned as a string of digits without additional formatting.
     *
     * @param string $input The raw TIN input string.
     * @return string The formatted TIN string, or an empty string if input is empty after normalization.
     */
    public function formatInput(string $input): string
    {
        $normalized = preg_replace('/[^0-9]/', '', $input);

        if ('' === $normalized) {
            return '';
        }

        // ABN format: 99 999 999 999
        if (strlen($normalized) >= 10) {
            $result = '';

            for ($i = 0; strlen($normalized) > $i && 11 > $i; ++$i) {
                if (2 === $i || 5 === $i || 8 === $i) {
                    $result .= ' ';
                }
                $result .= $normalized[$i];
            }

            return $result;
        }

        // TFN format: no specific formatting, just digits
        return $normalized;
    }

    /**
     * Returns the default input mask for Australian TINs in ABN format.
     *
     * @return string The input mask '99 999 999 999' for formatting ABNs.
     */
    public function getInputMask(): string
    {
        // Default to ABN format
        return '99 999 999 999';
    }

    /**
     * Returns a sample placeholder string for an Australian Business Number (ABN).
     *
     * @return string Example ABN placeholder.
     */
    public function getPlaceholder(): string
    {
        return '53 004 085 616';
    }

    /**
     * Returns an array of supported Australian TIN types, including TFN and ABN, with their codes, names, and descriptions.
     *
     * @return array List of TIN types with metadata for Australia.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'TFN',
                'name' => 'Tax File Number',
                'description' => 'Tax File Number for individuals and organizations',
            ],
            2 => [
                'code' => 'ABN',
                'name' => 'Australian Business Number',
                'description' => 'Australian Business Number for business entities',
            ],
        ];
    }

    /**
     * Determines whether the provided Australian TIN is a TFN or ABN and returns the corresponding type information.
     *
     * Normalizes the input and validates it as either a TFN (8 or 9 digits) or an ABN (11 digits) using the appropriate rules.
     *
     * @param string $tin The Tax Identification Number to evaluate.
     * @return array|null The TIN type information if valid, or null if the TIN does not match any supported type.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $this->normalizeTin($tin));
        $length = strlen($normalizedTin);

        // Check if it's TFN (8-9 digits)
        if ((8 === $length || 9 === $length) && $this->isValidTFN($normalizedTin)) {
            return $this->getTinTypes()[1]; // TFN
        }

        // Check if it's ABN (11 digits)
        if (11 === $length && $this->isValidABN($normalizedTin)) {
            return $this->getTinTypes()[2]; // ABN
        }

        return null;
    }

    /**
     * Checks if the normalized TIN has a valid length for Australian TFN or ABN.
     *
     * @param string $tin The input Tax Identification Number.
     * @return bool True if the TIN contains between 8 and 11 digits, inclusive; otherwise, false.
     */
    protected function hasValidLength(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);
        $length = strlen($normalizedTin);

        return 8 <= $length && 11 >= $length;
    }

    /**
     * Checks if the provided TIN matches the pattern for a valid Australian TFN or ABN.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN matches the expected pattern, false otherwise.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates the provided TIN using Australian TFN or ABN rules.
     *
     * Determines the type of TIN based on its length and applies the appropriate validation algorithm:
     * - For 8 or 9 digits, validates as a TFN.
     * - For 11 digits, validates as an ABN.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN passes the relevant validation rules, false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);
        $length = strlen($normalizedTin);

        // TFN validation (8-9 digits)
        if (8 === $length || 9 === $length) {
            return $this->isValidTFN($normalizedTin);
        }

        // ABN validation (11 digits)
        if (11 === $length) {
            return $this->isValidABN($normalizedTin);
        }

        return false;
    }

    /**
     * Checks if the provided Australian Business Number (ABN) is valid using the modulus 89 checksum algorithm.
     *
     * The function returns false if the ABN consists entirely of zeros. For other inputs, it applies the official ABN validation algorithm: subtracts 1 from the first digit, multiplies each digit by a specific weight, sums the results, and checks if the total is divisible by 89.
     *
     * @param string $abn The ABN to validate, consisting of 11 digits.
     * @return bool True if the ABN is valid according to the checksum algorithm, false otherwise.
     */
    private function isValidABN(string $abn): bool
    {
        // ABN should not be all zeros
        if (preg_match('/^0+$/', $abn)) {
            return false;
        }

        // Apply modulus 89 checksum algorithm
        $weights = [10, 1, 3, 5, 7, 9, 11, 13, 15, 17, 19];

        // Subtract 1 from the first digit
        $digits = str_split($abn);
        $digits[0] = (string) (((int) $digits[0]) - 1);

        // Calculate weighted sum
        $sum = 0;

        for ($i = 0; 11 > $i; ++$i) {
            $sum += ((int) $digits[$i]) * $weights[$i];
        }

        // Check if divisible by 89
        return 0 === ($sum % 89);
    }

    /**
     * Validates an Australian Tax File Number (TFN) using checksum and known invalid patterns.
     *
     * For 8-digit TFNs, applies a weighted checksum algorithm. For 9-digit TFNs, performs only basic validation, as full validation requires external data. Returns false for TFNs that are all zeros or match known invalid sequences.
     *
     * @param string $tfn The TFN to validate.
     * @return bool True if the TFN is considered valid, false otherwise.
     */
    private function isValidTFN(string $tfn): bool
    {
        // TFN should not be all zeros
        if (preg_match('/^0+$/', $tfn)) {
            return false;
        }

        // Known invalid TFNs
        $invalidTFNs = [
            '00000000', '11111111', '22222222', '33333333', '44444444',
            '55555555', '66666666', '77777777', '88888888', '99999999',
            '12345678', '87654321', '000000000', '111111111', '222222222',
            '333333333', '444444444', '555555555', '666666666', '777777777',
            '888888888', '999999999', '123456789', '987654321',
        ];

        if (in_array($tfn, $invalidTFNs, true)) {
            return false;
        }

        // If 8 digits, apply weighted checksum
        if (strlen($tfn) === 8) {
            $weights = [10, 7, 8, 4, 6, 3, 5, 1];
            $sum = 0;

            for ($i = 0; 8 > $i; ++$i) {
                $sum += ((int) $tfn[$i]) * $weights[$i];
            }

            return 0 === ($sum % 11);
        }

        // 9-digit TFNs follow different validation rules
        // Basic validation only (real validation would require ATO database)
        return true;
    }
}
