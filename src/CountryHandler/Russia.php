<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * Russia TIN validation.
 * Supports INN (Individual 12 digits, Company 10 digits).
 */
final class Russia extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'RU';

    /**
     * @var int
     */
    public const LENGTH = 12; // Maximum length

    /**
     * @var string
     */
    public const MASK = '999999999999'; // Default to personal

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^\d{10}$|^\d{12}$';

    /**
     * Returns a sample Russian TIN placeholder string.
     *
     * @return string Example TIN value for display or input guidance.
     */
    public function getPlaceholder(): string
    {
        return '123456789012';
    }

    /**
     * Returns an array of supported Russian TIN types for individuals and companies.
     *
     * @return array An array containing details for 'INN_PERSONAL' and 'INN_COMPANY' TIN types, including code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'INN_PERSONAL',
                'name' => 'Individual INN',
                'description' => 'Individual taxpayer identification number',
            ],
            2 => [
                'code' => 'INN_COMPANY',
                'name' => 'Company INN',
                'description' => 'Company taxpayer identification number',
            ],
        ];
    }

    /**
     * Determines the type of a Russian TIN (INN) based on its format and validity.
     *
     * Returns the corresponding TIN type array for a valid personal (12-digit) or company (10-digit) INN, or null if the TIN is invalid or does not match supported types.
     *
     * @param string $tin The Russian TIN to evaluate.
     * @return array|null The TIN type information if valid, or null if the TIN is invalid or unrecognized.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);

        if (strlen($normalizedTin) === 12 && $this->isValidPersonalINN($normalizedTin)) {
            return $this->getTinTypes()[1]; // Personal INN
        }

        if (strlen($normalizedTin) === 10 && $this->isValidCompanyINN($normalizedTin)) {
            return $this->getTinTypes()[2]; // Company INN
        }

        return null;
    }

    /**
     * Checks if the TIN has a valid length for Russian INNs.
     *
     * @param string $tin The taxpayer identification number to check.
     * @return bool True if the length is 10 or 12 digits, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        $length = strlen($tin);

        return 10 === $length || 12 === $length;
    }

    /**
     * Checks if the provided TIN matches the valid Russian TIN pattern for individuals or companies.
     *
     * @param string $tin The Taxpayer Identification Number to validate.
     * @return bool True if the TIN matches the required pattern; false otherwise.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates the TIN using rule-based checks specific to Russian INN formats.
     *
     * Applies the appropriate checksum algorithm based on the TIN's length:
     * - For 12-digit TINs, validates as a personal INN.
     * - For 10-digit TINs, validates as a company INN.
     *
     * @param string $tin The Taxpayer Identification Number to validate.
     * @return bool True if the TIN passes the relevant rule-based validation; false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $length = strlen($tin);

        if (12 === $length) {
            return $this->isValidPersonalINN($tin);
        }

        if (10 === $length) {
            return $this->isValidCompanyINN($tin);
        }

        return false;
    }

    /**
     * Checks if a 10-digit company INN (Russian Taxpayer Identification Number) is valid.
     *
     * Validates the region code and verifies the check digit using the official weighted sum algorithm.
     *
     * @param string $inn The 10-digit company INN to validate.
     * @return bool True if the INN is valid, false otherwise.
     */
    private function isValidCompanyINN(string $inn): bool
    {
        // Check region code (first 2 digits)
        $region = (int) substr($inn, 0, 2);

        if (1 > $region || 99 < $region) {
            return false;
        }

        // Validate check digit (10th position)
        $weights = [2, 4, 10, 3, 5, 9, 4, 6, 8];
        $sum = 0;

        for ($i = 0; 9 > $i; ++$i) {
            $sum += ((int) $inn[$i]) * $weights[$i];
        }

        $checkDigit = $sum % 11;

        if (9 < $checkDigit) {
            $checkDigit %= 10;
        }

        return (int) $inn[9] === $checkDigit;
    }

    /**
     * Validates a 12-digit Russian personal INN (individual taxpayer identification number).
     *
     * Checks that the region code is valid and both check digits are correct according to official algorithms.
     *
     * @param string $inn The 12-digit personal INN to validate.
     * @return bool True if the INN is valid, false otherwise.
     */
    private function isValidPersonalINN(string $inn): bool
    {
        // Check region code (first 2 digits)
        $region = (int) substr($inn, 0, 2);

        if (1 > $region || 99 < $region) {
            return false;
        }

        // Validate first check digit (11th position)
        $weights1 = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
        $sum1 = 0;

        for ($i = 0; 10 > $i; ++$i) {
            $sum1 += ((int) $inn[$i]) * $weights1[$i];
        }

        $checkDigit1 = $sum1 % 11;

        if (9 < $checkDigit1) {
            $checkDigit1 %= 10;
        }

        if ((int) $inn[10] !== $checkDigit1) {
            return false;
        }

        // Validate second check digit (12th position)
        $weights2 = [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
        $sum2 = 0;

        for ($i = 0; 11 > $i; ++$i) {
            $sum2 += ((int) $inn[$i]) * $weights2[$i];
        }

        $checkDigit2 = $sum2 % 11;

        if (9 < $checkDigit2) {
            $checkDigit2 %= 10;
        }

        return (int) $inn[11] === $checkDigit2;
    }
}
