<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * Japan TIN validation.
 * Supports My Number (12 digits) and Corporate Number (13 digits).
 */
final class Japan extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'JP';

    /**
     * @var int
     */
    public const LENGTH = 13; // Maximum length

    /**
     * @var string
     */
    public const MASK = '9999999999999'; // Default to corporate

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^\d{12,13}$';

    /**
     * Get input mask based on the TIN type.
     */
    public function getInputMask(): string
    {
        // Default to My Number format
        return '999999999999';
    }

    /**
     * Get placeholder based on the TIN type.
     */
    public function getPlaceholder(): string
    {
        return '123456789012';
    }

    /**
     * Get all TIN types supported by Japan.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'MYNUMBER',
                'name' => 'My Number',
                'description' => 'Individual identification number',
            ],
            2 => [
                'code' => 'CORPORATE',
                'name' => 'Corporate Number',
                'description' => 'Corporate identification number',
            ],
        ];
    }

    /**
     * Identify the TIN type for a given Japanese TIN.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);

        if (strlen($normalizedTin) === 12 && $this->isValidMyNumber($normalizedTin)) {
            return $this->getTinTypes()[1]; // My Number
        }

        if (strlen($normalizedTin) === 13 && $this->isValidCorporateNumber($normalizedTin)) {
            return $this->getTinTypes()[2]; // Corporate Number
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

        if (12 === $length) {
            return $this->isValidMyNumber($tin);
        }

        if (13 === $length) {
            return $this->isValidCorporateNumber($tin);
        }

        return false;
    }

    /**
     * Validate Corporate Number.
     * Uses modulus 9 check digit algorithm.
     */
    private function isValidCorporateNumber(string $number): bool
    {
        // First digit should be 1-9 (organization type)
        if ('0' === $number[0]) {
            return false;
        }

        // Check if all digits are the same (invalid)
        if (preg_match('/^(\d)\1{12}$/', $number)) {
            return false;
        }

        // Calculate check digit
        $sum = 0;

        for ($i = 0; 12 > $i; ++$i) {
            $weight = ($i % 2 === 0) ? 1 : 2;
            $sum += ((int) $number[11 - $i]) * $weight;
        }

        $checkDigit = 9 - ($sum % 9);

        return (int) $number[12] === $checkDigit;
    }

    /**
     * Validate My Number (individual).
     * Uses check digit algorithm.
     */
    private function isValidMyNumber(string $number): bool
    {
        // Check if all digits are the same (invalid)
        if (preg_match('/^(\d)\1{11}$/', $number)) {
            return false;
        }

        // Calculate check digit
        $sum = 0;

        for ($i = 0; 11 > $i; ++$i) {
            $p = 11 - $i;
            $q = (6 >= $p) ? $p + 1 : $p - 5;
            $sum += ((int) $number[$i]) * $q;
        }

        $remainder = $sum % 11;
        $checkDigit = (1 >= $remainder) ? 0 : 11 - $remainder;

        return (int) $number[11] === $checkDigit;
    }
}
