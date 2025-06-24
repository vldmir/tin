<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * Mexico TIN validation.
 * Supports RFC (Registro Federal de Contribuyentes) for personal and business entities.
 */
final class Mexico extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'MX';

    /**
     * @var int
     */
    public const LENGTH = 13; // Maximum length (personal)

    /**
     * @var string
     */
    public const MASK = 'AAAA999999XXX'; // Default to personal

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^([A-Z]{4}\d{6}[A-Z0-9]{3}|[A-Z]{3}\d{6}[A-Z0-9]{3})$';

    /**
     * Business RFC Pattern: AAA999999XXX (12 characters)
     * 3 letters + 6 digits + 3 alphanumeric.
     *
     * @var string
     */
    public const PATTERN_BUSINESS = '^[A-Z]{3}\d{6}[A-Z0-9]{3}$';

    /**
     * Personal RFC Pattern: AAAA999999XXX (13 characters)
     * 4 letters + 6 digits + 3 alphanumeric.
     *
     * @var string
     */
    public const PATTERN_PERSONAL = '^[A-Z]{4}\d{6}[A-Z0-9]{3}$';

    /**
     * Normalizes the input by removing non-alphanumeric characters and converting it to uppercase.
     *
     * @param string $input The input string to be formatted.
     * @return string The normalized RFC string.
     */
    public function formatInput(string $input): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/', '', $input));
        // No specific formatting for RFC, return as is
    }

    /**
     * Returns the default input mask for a Mexican RFC (personal format).
     *
     * @return string The input mask 'AAAA999999XXX' for personal RFCs.
     */
    public function getInputMask(): string
    {
        // Default to personal format
        return 'AAAA999999XXX';
    }

    /**
     * Returns a sample placeholder RFC string for Mexican TIN input fields.
     *
     * @return string Example RFC value for use as a placeholder.
     */
    public function getPlaceholder(): string
    {
        return 'GODE561231GR8';
    }

    /**
     * Returns an array of supported Mexican TIN types, including personal and business RFCs.
     *
     * @return array An associative array describing each supported TIN type with code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'RFC_PERSONAL',
                'name' => 'RFC Personal',
                'description' => 'Registro Federal de Contribuyentes for individuals',
            ],
            2 => [
                'code' => 'RFC_BUSINESS',
                'name' => 'RFC Empresarial',
                'description' => 'Registro Federal de Contribuyentes for businesses',
            ],
        ];
    }

    /**
     * Determines whether the given Mexican TIN is a personal or business RFC.
     *
     * Normalizes the input and validates its format and content. Returns the corresponding TIN type array if valid, or null if the TIN does not match any supported type.
     *
     * @param string $tin The input TIN to identify.
     * @return array|null The TIN type information if valid, or null if the TIN is invalid.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);

        if (strlen($normalizedTin) === 13 && $this->isValidPersonalRFC($normalizedTin)) {
            return $this->getTinTypes()[1]; // Personal RFC
        }

        if (strlen($normalizedTin) === 12 && $this->isValidBusinessRFC($normalizedTin)) {
            return $this->getTinTypes()[2]; // Business RFC
        }

        return null;
    }

    /**
     * Checks if the TIN has a valid length for a Mexican RFC.
     *
     * A valid RFC must be either 12 or 13 characters long.
     *
     * @param string $tin The TIN to check.
     * @return bool True if the TIN length is valid, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        $length = strlen($tin);

        return 12 === $length || 13 === $length;
    }

    /**
     * Checks if the provided TIN matches the combined RFC pattern for Mexico.
     *
     * @param string $tin The TIN to validate.
     * @return bool True if the TIN matches the RFC pattern; otherwise, false.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Applies RFC-specific validation rules based on the TIN's length.
     *
     * Returns true if the TIN passes the appropriate validation for either a personal (13 characters) or business (12 characters) RFC; otherwise, returns false.
     *
     * @param string $tin The normalized TIN to validate.
     * @return bool True if the TIN is valid according to RFC rules, false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $length = strlen($tin);

        if (13 === $length) {
            return $this->isValidPersonalRFC($tin);
        }

        if (12 === $length) {
            return $this->isValidBusinessRFC($tin);
        }

        return false;
    }

    /**
     * Validates a business RFC (Registro Federal de Contribuyentes) for Mexico.
     *
     * Checks that the RFC matches the business pattern, contains a valid date segment, and has a valid homoclave.
     *
     * @param string $rfc The business RFC to validate.
     * @return bool True if the RFC is valid, false otherwise.
     */
    private function isValidBusinessRFC(string $rfc): bool
    {
        if (!$this->matchPattern($rfc, self::PATTERN_BUSINESS)) {
            return false;
        }

        // Extract date components
        $year = substr($rfc, 3, 2);
        $month = substr($rfc, 5, 2);
        $day = substr($rfc, 7, 2);

        // Validate date
        if (!$this->isValidDate($year, $month, $day)) {
            return false;
        }

        // Validate homoclave (last 3 characters)
        return $this->isValidHomoclave(substr($rfc, 9, 3));
    }

    /**
     * Checks if the provided year, month, and day values form a valid calendar date.
     *
     * Validates that the month is between 1 and 12, the day is within the valid range for the given month (including up to 29 days for February), and both values are positive integers.
     *
     * @param string $year The year component as a string.
     * @param string $month The month component as a string.
     * @param string $day The day component as a string.
     * @return bool True if the date components represent a valid date; otherwise, false.
     */
    private function isValidDate(string $year, string $month, string $day): bool
    {
        $monthInt = (int) $month;
        $dayInt = (int) $day;

        // Validate month
        if (1 > $monthInt || 12 < $monthInt) {
            return false;
        }

        // Validate day
        if (1 > $dayInt || 31 < $dayInt) {
            return false;
        }

        // More specific day validation based on month
        $daysInMonth = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        if ($daysInMonth[$monthInt - 1] < $dayInt) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the homoclave (last 3 characters of an RFC) consists of exactly three alphanumeric characters.
     *
     * @param string $homoclave The homoclave segment to validate.
     * @return bool True if the homoclave is valid; otherwise, false.
     */
    private function isValidHomoclave(string $homoclave): bool
    {
        // Homoclave should be 3 alphanumeric characters
        if (!preg_match('/^[A-Z0-9]{3}$/', $homoclave)) {
            return false;
        }

        // The third character is a check digit
        // In a real implementation, this would involve a complex algorithm
        // For now, we'll do basic validation
        return true;
    }

    /**
     * Validates a personal RFC (Registro Federal de Contribuyentes) for Mexico.
     *
     * Checks that the RFC matches the personal format, contains a valid date segment, and has a valid homoclave.
     *
     * @param string $rfc The personal RFC to validate.
     * @return bool True if the RFC is valid; otherwise, false.
     */
    private function isValidPersonalRFC(string $rfc): bool
    {
        if (!$this->matchPattern($rfc, self::PATTERN_PERSONAL)) {
            return false;
        }

        // Extract date components
        $year = substr($rfc, 4, 2);
        $month = substr($rfc, 6, 2);
        $day = substr($rfc, 8, 2);

        // Validate date
        if (!$this->isValidDate($year, $month, $day)) {
            return false;
        }

        // Validate homoclave (last 3 characters)
        return $this->isValidHomoclave(substr($rfc, 10, 3));
    }
}
