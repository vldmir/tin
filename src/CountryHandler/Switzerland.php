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
     * Combined pattern for all types (after normalization removes separators).
     *
     * @var string
     */
    public const PATTERN = '^(756\d{10}|CHE\d{9})$';

    /**
     * AVS/AHV Pattern: 756 followed by 10 digits (13 total).
     *
     * @var string
     */
    public const PATTERN_AVS = '^756\d{10}$';

    /**
     * UID Pattern: CHE followed by 9 digits (12 chars total).
     *
     * @var string
     */
    public const PATTERN_UID = '^CHE\d{9}$';

    /**
     * Format input according to TIN type.
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
     * Get input mask based on the TIN type.
     */
    public function getInputMask(): string
    {
        // Default to AVS format
        return '756.9999.9999.99';
    }

    /**
     * Get placeholder based on the TIN type.
     */
    public function getPlaceholder(): string
    {
        return '756.1234.5678.97';
    }

    /**
     * Get all TIN types supported by Switzerland.
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
     * Identify the TIN type for a given Swiss TIN.
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

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN_AVS)
               || $this->matchPattern($tin, self::PATTERN_UID);
    }

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
     * Validate AVS/AHV number.
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
     * Validate UID number.
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
     * Validate EAN-13 checksum.
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
     * Validate UID modulo 11 checksum.
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
