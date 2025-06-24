<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * South Korea TIN validation.
 * Supports RRN (Resident Registration Number) and BRN (Business Registration Number).
 */
final class SouthKorea extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'KR';

    /**
     * @var int
     */
    public const LENGTH = 13; // Maximum length (RRN)

    /**
     * @var string
     */
    public const MASK = '999999-9999999'; // Default to RRN

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^(\d{6}-?\d{7}|\d{3}-?\d{2}-?\d{5})$';

    /**
     * BRN Pattern: 999-99-99999 (10 digits).
     *
     * @var string
     */
    public const PATTERN_BRN = '^\d{3}-?\d{2}-?\d{5}$';

    /**
     * RRN Pattern: XXXXXX-XXXXXXX (13 digits with dash).
     *
     * @var string
     */
    public const PATTERN_RRN = '^\d{6}-?\d{7}$';

    /**
     * Formats a South Korean TIN input as either a Resident Registration Number (RRN) or Business Registration Number (BRN).
     *
     * Normalizes the input by removing non-digit characters, then applies the appropriate formatting:
     * - RRN: `999999-9999997` for 7–13 digits.
     * - BRN: `999-99-99999` for exactly 10 digits.
     * Returns the normalized digits if no format applies or the input is empty.
     *
     * @param string $input The raw TIN input string.
     * @return string The formatted TIN string or an empty string if input is empty.
     */
    public function formatInput(string $input): string
    {
        $normalized = preg_replace('/[^0-9]/', '', $input);

        if ('' === $normalized) {
            return '';
        }

        // RRN format: 999999-9999999
        if (strlen($normalized) > 6 && strlen($normalized) <= 13) {
            return substr($normalized, 0, 6) . '-' . substr($normalized, 6, 7);
        }

        // BRN format: 999-99-99999
        if (strlen($normalized) === 10) {
            return substr($normalized, 0, 3) . '-'
                   . substr($normalized, 3, 2) . '-'
                   . substr($normalized, 5, 5);
        }

        return $normalized;
    }

    /**
     * Returns the default input mask for South Korean Resident Registration Numbers (RRN).
     *
     * @return string The input mask '999999-9999999' for RRN formatting.
     */
    public function getInputMask(): string
    {
        // Default to RRN format
        return '999999-9999999';
    }

    /**
     * Returns a placeholder example for a South Korean Resident Registration Number (RRN).
     *
     * @return string Example placeholder in RRN format.
     */
    public function getPlaceholder(): string
    {
        return '900101-1234567';
    }

    /**
     * Returns an array of supported South Korean TIN types, including codes, names, and descriptions for both Resident Registration Numbers (RRN) and Business Registration Numbers (BRN).
     *
     * @return array List of TIN types with metadata.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'RRN',
                'name' => 'Resident Registration Number',
                'description' => 'Korean resident registration number for individuals',
            ],
            2 => [
                'code' => 'BRN',
                'name' => 'Business Registration Number',
                'description' => 'Korean business registration number for companies',
            ],
        ];
    }

    /**
     * Determines whether the provided TIN is a valid South Korean Resident Registration Number (RRN) or Business Registration Number (BRN), and returns the corresponding TIN type information.
     *
     * @param string $tin The input Tax Identification Number to evaluate.
     * @return array|null The TIN type metadata if valid (RRN or BRN), or null if the TIN is invalid or unrecognized.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $this->normalizeTin($tin));

        if (strlen($normalizedTin) === 13 && $this->isValidRRN($normalizedTin)) {
            return $this->getTinTypes()[1]; // RRN
        }

        if (strlen($normalizedTin) === 10 && $this->isValidBRN($normalizedTin)) {
            return $this->getTinTypes()[2]; // BRN
        }

        return null;
    }

    /**
     * Checks if the normalized TIN has a valid length for South Korean RRN (13 digits) or BRN (10 digits).
     *
     * @param string $tin The input Tax Identification Number.
     * @return bool True if the TIN has a valid length, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);
        $length = strlen($normalizedTin);

        return 10 === $length || 13 === $length;
    }

    /**
     * Checks if the provided TIN matches the South Korean RRN or BRN pattern.
     *
     * @param string $tin The tax identification number to validate.
     * @return bool True if the TIN matches the expected pattern, false otherwise.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates the TIN according to South Korean RRN or BRN rules.
     *
     * Determines the type of TIN based on its length and applies the appropriate validation algorithm for Resident Registration Numbers (RRN) or Business Registration Numbers (BRN).
     *
     * @param string $tin The input TIN to validate.
     * @return bool True if the TIN is valid according to its type's rules, false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

        if (strlen($normalizedTin) === 13) {
            return $this->isValidRRN($normalizedTin);
        }

        if (strlen($normalizedTin) === 10) {
            return $this->isValidBRN($normalizedTin);
        }

        return false;
    }

    /**
     * Determines the century of birth based on the gender/century digit in a South Korean Resident Registration Number (RRN).
     *
     * @param int $genderDigit The digit indicating gender and century in the RRN.
     * @return int|null The century (e.g., 1900, 2000, 1800) or null if the digit is invalid.
     */
    private function getCentury(int $genderDigit): ?int
    {
        switch ($genderDigit) {
            case 1:
            case 2:
                return 1900; // Born 1900-1999

            case 3:
            case 4:
                return 2000; // Born 2000-2099

            case 5:
            case 6:
                return 1900; // Foreigner born 1900-1999

            case 7:
            case 8:
                return 2000; // Foreigner born 2000-2099

            case 9:
            case 0:
                return 1800; // Born 1800-1899

            default:
                return null;
        }
    }

    /**
     * Checks if the provided year, month, and day constitute a valid birth date that is not in the future.
     *
     * @param int $year The year component of the date.
     * @param int $month The month component of the date.
     * @param int $day The day component of the date.
     * @return bool True if the date is valid and not in the future, false otherwise.
     */
    private function isValidBirthDate(int $year, int $month, int $day): bool
    {
        // Basic range validation
        if (1 > $month || 12 < $month) {
            return false;
        }

        if (1 > $day || 31 < $day) {
            return false;
        }

        // Check if date is valid
        if (!checkdate($month, $day, $year)) {
            return false;
        }

        // Birth year should not be in the future
        if ((int) date('Y') < $year) {
            return false;
        }

        return true;
    }

    /**
     * Validates a South Korean Business Registration Number (BRN) using its checksum algorithm.
     *
     * @param string $brn The BRN as a 10-digit string.
     * @return bool True if the BRN is valid according to the checksum; otherwise, false.
     */
    private function isValidBRN(string $brn): bool
    {
        // Apply checksum algorithm
        $weights = [1, 3, 7, 1, 3, 7, 1, 3, 5];
        $sum = 0;

        for ($i = 0; 9 > $i; ++$i) {
            $sum += ((int) $brn[$i]) * $weights[$i];
        }

        // Add the quotient of the 9th digit * 5 / 10
        $sum += ((int) (((int) $brn[8]) * 5 / 10));

        $checkDigit = (10 - ($sum % 10)) % 10;

        return (int) $brn[9] === $checkDigit;
    }

    /**
     * Validates a South Korean Resident Registration Number (RRN).
     *
     * Checks the RRN for correct birth date encoding, valid century digit, and a valid checksum.
     *
     * @param string $rrn The RRN to validate, consisting of 13 digits.
     * @return bool True if the RRN is valid; false otherwise.
     */
    private function isValidRRN(string $rrn): bool
    {
        // Extract date components
        $year = substr($rrn, 0, 2);
        $month = substr($rrn, 2, 2);
        $day = substr($rrn, 4, 2);
        $genderCentury = (int) substr($rrn, 6, 1);

        // Determine century based on gender digit
        $century = $this->getCentury($genderCentury);

        if (null === $century) {
            return false;
        }

        $fullYear = $century + (int) $year;

        // Validate date
        if (!$this->isValidBirthDate($fullYear, (int) $month, (int) $day)) {
            return false;
        }

        // Validate checksum
        return $this->isValidRRNChecksum($rrn);
    }

    /**
     * Checks if a South Korean Resident Registration Number (RRN) has a valid checksum.
     *
     * @param string $rrn The 13-digit RRN to validate.
     * @return bool True if the checksum is valid, false otherwise.
     */
    private function isValidRRNChecksum(string $rrn): bool
    {
        $weights = [2, 3, 4, 5, 6, 7, 8, 9, 2, 3, 4, 5];
        $sum = 0;

        for ($i = 0; 12 > $i; ++$i) {
            $sum += ((int) $rrn[$i]) * $weights[$i];
        }

        $checkDigit = (11 - ($sum % 11)) % 10;

        return (int) $rrn[12] === $checkDigit;
    }
}
