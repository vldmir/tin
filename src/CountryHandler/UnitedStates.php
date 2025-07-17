<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function in_array;
use function strlen;

/**
 * United States TIN validation.
 * Supports SSN (Social Security Number), ITIN (Individual Taxpayer Identification Number),
 * and EIN (Employer Identification Number).
 */
final class UnitedStates extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'US';

    /**
     * @var int
     */
    public const LENGTH = 9;

    /**
     * @var string
     */
    public const MASK = '999-99-9999';

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^(\d{3}-?\d{2}-?\d{4}|\d{2}-?\d{7})$';

    /**
     * EIN Pattern: 99-9999999.
     *
     * @var string
     */
    public const PATTERN_EIN = '^\d{2}-?\d{7}$';

    /**
     * SSN/ITIN Pattern: 999-99-9999.
     *
     * @var string
     */
    public const PATTERN_SSN_ITIN = '^\d{3}-?\d{2}-?\d{4}$';

    /**
     * Formats a United States TIN input string as either EIN (99-9999999) or SSN/ITIN (999-99-9999) based on its prefix and length.
     *
     * Removes all non-digit characters from the input and applies the appropriate formatting. Returns an empty string if the input contains no digits.
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

        // Try to determine format based on pattern
        if (strlen($normalized) >= 2) {
            // Check if it could be EIN (prefix validation)
            $prefix = (int) substr($normalized, 0, 2);

            if ($this->isValidEINPrefix($prefix) && strlen($normalized) <= 9) {
                // Format as EIN: 99-9999999
                if (strlen($normalized) >= 2) {
                    $result = substr($normalized, 0, 2);

                    if (strlen($normalized) > 2) {
                        $result .= '-' . substr($normalized, 2, 7);
                    }

                    return $result;
                }
            }
        }

        // Default to SSN/ITIN format: 999-99-9999
        $result = '';

        if (strlen($normalized) >= 3) {
            $result = substr($normalized, 0, 3);

            if (strlen($normalized) >= 5) {
                $result .= '-' . substr($normalized, 3, 2);

                if (strlen($normalized) >= 6) {
                    $result .= '-' . substr($normalized, 5, 4);
                }
            } elseif (strlen($normalized) > 3) {
                $result .= '-' . substr($normalized, 3);
            }
        } else {
            $result = $normalized;
        }

        return $result;
    }

    /**
     * Returns the default input mask for United States TINs in SSN/ITIN format.
     *
     * @return string The input mask '999-99-9999'.
     */
    public function getInputMask(): string
    {
        // Default to SSN/ITIN format
        return '999-99-9999';
    }

    /**
     * Returns a placeholder string representing the standard US TIN format.
     *
     * @return string The placeholder '123-45-6789'.
     */
    public function getPlaceholder(): string
    {
        return '123-45-6789';
    }

    /**
     * Returns an array describing the supported United States TIN types: SSN, ITIN, and EIN.
     *
     * @return array List of TIN types, each with code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'SSN',
                'name' => 'Social Security Number',
                'description' => 'Social Security Number for US citizens and permanent residents',
            ],
            2 => [
                'code' => 'ITIN',
                'name' => 'Individual Taxpayer Identification Number',
                'description' => 'Tax identification number for individuals who are not eligible for SSN',
            ],
            3 => [
                'code' => 'EIN',
                'name' => 'Employer Identification Number',
                'description' => 'Federal tax identification number for businesses',
            ],
        ];
    }

    /**
     * Determines the type of a US Taxpayer Identification Number (TIN).
     *
     * Analyzes the provided TIN to identify whether it is an SSN, ITIN, or EIN based on its format and validation rules.
     *
     * @param string $tin The TIN to be analyzed.
     * @return array|null An array describing the TIN type if recognized, or null if the TIN is invalid or unrecognized.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);
        $digitsOnly = preg_replace('/[^0-9]/', '', $normalizedTin);

        if (strlen($digitsOnly) !== 9) {
            return null;
        }

        // Check if it's EIN format (2 digits + 7 digits)
        if ($this->matchPattern($tin, self::PATTERN_EIN)) {
            if ($this->isValidEIN($digitsOnly)) {
                return $this->getTinTypes()[3]; // EIN
            }
        }

        // Check if it's ITIN (starts with 9)
        if (substr($digitsOnly, 0, 1) === '9' && $this->isValidITIN($digitsOnly)) {
            return $this->getTinTypes()[2]; // ITIN
        }

        // Check if it's SSN
        if ($this->matchPattern($tin, self::PATTERN_SSN_ITIN) && $this->isValidSSNorITIN($digitsOnly)) {
            return $this->getTinTypes()[1]; // SSN
        }

        return null;
    }

    /**
     * Checks if the normalized TIN has the required length of 9 digits.
     *
     * @param string $tin The input Taxpayer Identification Number.
     * @return bool True if the TIN contains exactly 9 digits after normalization, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

        return strlen($normalizedTin) === self::LENGTH;
    }

    /**
     * Checks if the provided TIN matches the combined US TIN pattern for SSN, ITIN, or EIN formats.
     *
     * @param string $tin The Taxpayer Identification Number to validate.
     * @return bool True if the TIN matches the expected pattern; otherwise, false.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates a US TIN by applying specific rules for SSN, ITIN, or EIN formats.
     *
     * Returns true if the input passes the rule-based validation for its detected TIN type; otherwise, returns false.
     *
     * @param string $tin The Taxpayer Identification Number to validate.
     * @return bool True if the TIN is valid according to its type-specific rules, false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

        // Check if it's SSN/ITIN format (9 digits starting with specific rules)
        if ($this->matchPattern($tin, self::PATTERN_SSN_ITIN)) {
            return $this->isValidSSNorITIN($normalizedTin);
        }

        // Check if it's EIN format
        if ($this->matchPattern($tin, self::PATTERN_EIN)) {
            return $this->isValidEIN($normalizedTin);
        }

        return false;
    }

    /**
     * Checks if the given TIN has a valid EIN prefix.
     *
     * Extracts the first two digits of the TIN and verifies that they are within the list of valid EIN prefixes.
     *
     * @param string $tin The Taxpayer Identification Number to validate.
     * @return bool True if the prefix is valid for an EIN, false otherwise.
     */
    private function isValidEIN(string $tin): bool
    {
        // Extract prefix
        $prefix = (int) substr($tin, 0, 2);

        // Valid EIN prefixes (campus codes)
        $validPrefixes = [
            1, 2, 3, 4, 5, 6, 10, 11, 12, 13, 14, 15, 16, 20, 21, 22, 23, 24, 25, 26, 27,
            30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48,
            50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68,
            71, 72, 73, 74, 75, 76, 77, 80, 81, 82, 83, 84, 85, 86, 87, 88, 90, 91, 92,
            93, 94, 95, 98, 99,
        ];

        return in_array($prefix, $validPrefixes, true);
    }

    /**
     * Determines if the given prefix is a valid Employer Identification Number (EIN) prefix.
     *
     * @param int $prefix The first two digits of the EIN.
     * @return bool True if the prefix is valid for an EIN, false otherwise.
     */
    private function isValidEINPrefix(int $prefix): bool
    {
        $validPrefixes = [
            1, 2, 3, 4, 5, 6, 10, 11, 12, 13, 14, 15, 16, 20, 21, 22, 23, 24, 25, 26, 27,
            30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48,
            50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68,
            71, 72, 73, 74, 75, 76, 77, 80, 81, 82, 83, 84, 85, 86, 87, 88, 90, 91, 92,
            93, 94, 95, 98, 99,
        ];

        return in_array($prefix, $validPrefixes, true);
    }

    /**
     * Determines if a TIN is a valid Individual Taxpayer Identification Number (ITIN) based on US-specific rules.
     *
     * An ITIN must start with '9', and its fourth and fifth digits must fall within the ranges 50–65, 70–88, 90–92, or 94–99.
     *
     * @param string $tin The normalized TIN to validate.
     * @return bool True if the TIN meets ITIN criteria; otherwise, false.
     */
    private function isValidITIN(string $tin): bool
    {
        // ITIN must start with 9
        if (substr($tin, 0, 1) !== '9') {
            return false;
        }

        // Fourth and fifth digits must be in range 50-65, 70-88, 90-92, 94-99
        $fourthFifth = (int) substr($tin, 3, 2);
        $validRanges = [
            [50, 65],
            [70, 88],
            [90, 92],
            [94, 99],
        ];

        foreach ($validRanges as $range) {
            if ($fourthFifth >= $range[0] && $fourthFifth <= $range[1]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether a TIN satisfies SSN or ITIN-specific validation rules.
     *
     * Returns true if the input passes all SSN/ITIN requirements, including non-zero digit groups, valid area codes, and special handling for ITINs starting with '9'.
     *
     * @param string $tin The normalized TIN to validate.
     * @return bool True if the TIN is a valid SSN or ITIN; false otherwise.
     */
    private function isValidSSNorITIN(string $tin): bool
    {
        // Extract parts
        $area = substr($tin, 0, 3);
        $group = substr($tin, 3, 2);
        $serial = substr($tin, 5, 4);

        // SSN/ITIN cannot have all zeros in any group
        if ('000' === $area || '00' === $group || '0000' === $serial) {
            return false;
        }

        // SSN cannot start with 666 or 900-999
        $areaInt = (int) $area;

        if (666 === $areaInt || 900 <= $areaInt) {
            // Exception: ITIN always starts with 9
            if (900 <= $areaInt && 999 >= $areaInt) {
                return $this->isValidITIN($tin);
            }

            return false;
        }

        // Invalid SSN area codes
        if (in_array($area, ['123', '456', '789'], true)) {
            return false;
        }

        return true;
    }
}
