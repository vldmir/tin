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
     * Format input according to TIN type.
     */
    public function formatInput(string $input): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/', '', $input));
        // No specific formatting for RFC, return as is
    }

    /**
     * Get input mask based on the TIN type.
     */
    public function getInputMask(): string
    {
        // Default to personal format
        return 'AAAA999999XXX';
    }

    /**
     * Get placeholder based on the TIN type.
     */
    public function getPlaceholder(): string
    {
        return 'GODE561231GR8';
    }

    /**
     * Get all TIN types supported by Mexico.
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
     * Identify the TIN type for a given Mexican TIN.
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

    protected function hasValidLength(string $tin): bool
    {
        $length = strlen($tin);

        return 12 === $length || 13 === $length;
    }

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

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
     * Validate business RFC.
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
     * Validate date components.
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
     * Validate homoclave (last 3 characters).
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
     * Validate personal RFC.
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
