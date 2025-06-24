<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * Switzerland TIN validation.
 * Supports AVS/AHV (social security number) and UID (business identification).
 */
final class Switzerland extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'CH';

    /**
     * @var int
     */
    public const LENGTH = 13; // Maximum normalized length

    /**
     * @var string
     */
    public const MASK = '756.9999.9999.99'; // Default to AVS

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^(756\.\d{4}\.\d{4}\.\d{2}|CHE-?\d{3}\.?\d{3}\.?\d{3})$';

    /**
     * AVS/AHV Pattern: 756.9999.9999.99 (13 digits).
     *
     * @var string
     */
    public const PATTERN_AVS = '^756\.\d{4}\.\d{4}\.\d{2}$';

    /**
     * UID Pattern: CHE-999.999.999 (9 digits after CHE).
     *
     * @var string
     */
    public const PATTERN_UID = '^CHE-?\d{3}\.?\d{3}\.?\d{3}$';

    /**
     * Formats a Swiss TIN input string as either AVS/AHV or UID, applying the appropriate separators.
     *
     * Normalizes the input by removing non-alphanumeric characters and converting to uppercase. If the input starts with '756' and is up to 13 characters, it is formatted as an AVS/AHV number (`756.9999.9999.99`). If it starts with 'CHE' and the following digits are up to 9 characters, it is formatted as a UID (`CHE-999.999.999`). Otherwise, returns the normalized input.
     *
     * @param string $input The raw TIN input string.
     * @return string The formatted TIN string.
     */
    public function formatInput(string $input): string
    {
        $normalized = strtoupper(preg_replace('/[^A-Z0-9]/', '', $input));

        if ('' === $normalized) {
            return '';
        }

        // Check if it's AVS format (starts with 756)
        if (substr($normalized, 0, 3) === '756' && strlen($normalized) <= 13) {
            // Format as: 756.9999.9999.99
            $result = '';

            for ($i = 0; strlen($normalized) > $i && 13 > $i; ++$i) {
                if (3 === $i || 7 === $i || 11 === $i) {
                    $result .= '.';
                }
                $result .= $normalized[$i];
            }

            return $result;
        }

        // Check if it's UID format (starts with CHE)
        if (substr($normalized, 0, 3) === 'CHE') {
            $digits = substr($normalized, 3);

            if (strlen($digits) <= 9) {
                // Format as: CHE-999.999.999
                $result = 'CHE';

                if ('' !== $digits) {
                    $result .= '-';

                    for ($i = 0; strlen($digits) > $i && 9 > $i; ++$i) {
                        if (3 === $i || 6 === $i) {
                            $result .= '.';
                        }
                        $result .= $digits[$i];
                    }
                }

                return $result;
            }
        }

        return $normalized;
    }

    /**
     * Returns the default input mask for Swiss TINs in AVS format.
     *
     * @return string The input mask string '756.9999.9999.99'.
     */
    public function getInputMask(): string
    {
        // Default to AVS format
        return '756.9999.9999.99';
    }

    /**
     * Returns a placeholder string representing the Swiss AVS TIN format.
     *
     * @return string The placeholder in the format '756.1234.5678.90'.
     */
    public function getPlaceholder(): string
    {
        return '756.1234.5678.90';
    }

    /**
     * Returns an array describing the supported Swiss TIN types: AVS/AHV (social security number) and UID (business identification number).
     *
     * @return array List of TIN types with code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'AVS/AHV',
                'name' => 'AVS/AHV Number',
                'description' => 'Swiss social security number for individuals',
            ],
            2 => [
                'code' => 'UID',
                'name' => 'Unternehmens-Identifikationsnummer',
                'description' => 'Swiss business identification number',
            ],
        ];
    }

    /**
     * Determines the type of a Swiss TIN (AVS/AHV or UID) based on its format and validity.
     *
     * Returns an array describing the TIN type if the input matches and validates as either AVS/AHV or UID, or null if the type cannot be identified.
     *
     * @param string $tin The Swiss TIN to evaluate.
     * @return array|null The TIN type information array, or null if the TIN is invalid or unrecognized.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);

        if (preg_match('/^756/', $tin) && $this->isValidAVS($tin)) {
            return $this->getTinTypes()[1]; // AVS/AHV
        }

        if (preg_match('/^CHE/i', $tin) && $this->isValidUID($tin)) {
            return $this->getTinTypes()[2]; // UID
        }

        return null;
    }

    /**
     * Checks if the provided TIN has a valid length for Swiss AVS or UID formats.
     *
     * For AVS numbers (starting with '756'), the numeric part must be exactly 13 digits.
     * For UID numbers (starting with 'CHE'), the numeric part must be exactly 9 digits.
     *
     * @param string $tin The TIN to check.
     * @return bool True if the TIN has a valid length for its type, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        // Check AVS format
        if (preg_match('/^756/', $tin)) {
            $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

            return strlen($normalizedTin) === 13;
        }

        // Check UID format
        if (preg_match('/^CHE/i', $tin)) {
            $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

            return strlen($normalizedTin) === 9;
        }

        return false;
    }

    /**
     * Checks if the provided TIN matches the AVS or UID format patterns.
     *
     * @param string $tin The tax identification number to check.
     * @return bool True if the TIN matches either the AVS or UID pattern, false otherwise.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN_AVS)
               || $this->matchPattern($tin, self::PATTERN_UID);
    }

    /**
     * Validates the TIN according to Swiss AVS or UID rules.
     *
     * Determines the TIN type based on its prefix and applies the corresponding checksum validation.
     *
     * @param string $tin The TIN to validate.
     * @return bool True if the TIN passes the type-specific validation rules, false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        // Check if it's AVS format
        if (preg_match('/^756/', $tin)) {
            return $this->isValidAVS($tin);
        }

        // Check if it's UID format
        if (preg_match('/^CHE/i', $tin)) {
            return $this->isValidUID($tin);
        }

        return false;
    }

    /**
     * Validates a Swiss AVS/AHV (social security) number.
     *
     * Removes formatting, checks for correct length and Swiss prefix, and verifies the EAN-13 checksum.
     *
     * @param string $avs The AVS/AHV number to validate.
     * @return bool True if the AVS/AHV number is valid, false otherwise.
     */
    private function isValidAVS(string $avs): bool
    {
        // Remove dots
        $normalizedAVS = preg_replace('/[^0-9]/', '', $avs);

        if (strlen($normalizedAVS) !== 13) {
            return false;
        }

        // Must start with 756 (Swiss country code)
        if (substr($normalizedAVS, 0, 3) !== '756') {
            return false;
        }

        // Validate EAN-13 checksum
        return $this->validateEAN13Checksum($normalizedAVS);
    }

    /**
     * Validates a Swiss UID (business identification number) by checking its length and verifying the modulo 11 checksum.
     *
     * @param string $uid The UID to validate.
     * @return bool True if the UID is valid, false otherwise.
     */
    private function isValidUID(string $uid): bool
    {
        // Remove CHE prefix and formatting
        $normalizedUID = preg_replace('/^CHE-?/i', '', $uid);
        $normalizedUID = preg_replace('/[^0-9]/', '', $normalizedUID);

        if (strlen($normalizedUID) !== 9) {
            return false;
        }

        // Validate modulo 11 checksum
        return $this->validateUID11Checksum($normalizedUID);
    }

    /**
     * Validates the EAN-13 checksum for a 13-digit number.
     *
     * Calculates the check digit using the EAN-13 algorithm and compares it to the last digit of the input.
     *
     * @param string $number The 13-digit number to validate.
     * @return bool True if the checksum is valid, false otherwise.
     */
    private function validateEAN13Checksum(string $number): bool
    {
        $sum = 0;

        // Process first 12 digits
        for ($i = 0; 12 > $i; ++$i) {
            $digit = (int) $number[$i];
            // Odd positions (1-indexed) have weight 1, even positions have weight 3
            $weight = ($i % 2 === 0) ? 1 : 3;
            $sum += $digit * $weight;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return (int) $number[12] === $checkDigit;
    }

    /**
     * Validates the Swiss UID number using the modulo 11 checksum algorithm.
     *
     * Applies predefined weights to the first 8 digits, calculates the checksum, and verifies it against the 9th digit. Returns false if the computed checksum is 10, as such UIDs are invalid.
     *
     * @param string $number The 9-digit UID number as a string.
     * @return bool True if the checksum is valid, false otherwise.
     */
    private function validateUID11Checksum(string $number): bool
    {
        $weights = [5, 4, 3, 2, 7, 6, 5, 4];
        $sum = 0;

        // Process first 8 digits
        for ($i = 0; 8 > $i; ++$i) {
            $sum += ((int) $number[$i]) * $weights[$i];
        }

        $remainder = $sum % 11;
        $checkDigit = (11 - $remainder) % 11;

        // If checksum is 10, the number is invalid
        if (10 === $checkDigit) {
            return false;
        }

        return (int) $number[8] === $checkDigit;
    }
}
