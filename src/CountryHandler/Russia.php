<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

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
     * Combined pattern for all types
     * @var string
     */
    public const PATTERN = '^\d{10}$|^\d{12}$';

    /**
     * @var int
     */
    public const LENGTH = 12; // Maximum length

    /**
     * @var string
     */
    public const MASK = '999999999999'; // Default to personal

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    protected function hasValidLength(string $tin): bool
    {
        $length = strlen($tin);
        return $length === 10 || $length === 12;
    }

    protected function hasValidRule(string $tin): bool
    {
        $length = strlen($tin);
        
        if ($length === 12) {
            return $this->isValidPersonalINN($tin);
        }
        
        if ($length === 10) {
            return $this->isValidCompanyINN($tin);
        }
        
        return false;
    }

    /**
     * Validate personal INN (12 digits).
     */
    private function isValidPersonalINN(string $inn): bool
    {
        // Check region code (first 2 digits)
        $region = (int) substr($inn, 0, 2);
        if ($region < 1 || $region > 99) {
            return false;
        }
        
        // Validate first check digit (11th position)
        $weights1 = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
        $sum1 = 0;
        
        for ($i = 0; $i < 10; $i++) {
            $sum1 += ((int) $inn[$i]) * $weights1[$i];
        }
        
        $checkDigit1 = $sum1 % 11;
        if ($checkDigit1 > 9) {
            $checkDigit1 = $checkDigit1 % 10;
        }
        
        if ($checkDigit1 !== (int) $inn[10]) {
            return false;
        }
        
        // Validate second check digit (12th position)
        $weights2 = [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
        $sum2 = 0;
        
        for ($i = 0; $i < 11; $i++) {
            $sum2 += ((int) $inn[$i]) * $weights2[$i];
        }
        
        $checkDigit2 = $sum2 % 11;
        if ($checkDigit2 > 9) {
            $checkDigit2 = $checkDigit2 % 10;
        }
        
        return $checkDigit2 === (int) $inn[11];
    }

    /**
     * Validate company INN (10 digits).
     */
    private function isValidCompanyINN(string $inn): bool
    {
        // Check region code (first 2 digits)
        $region = (int) substr($inn, 0, 2);
        if ($region < 1 || $region > 99) {
            return false;
        }
        
        // Validate check digit (10th position)
        $weights = [2, 4, 10, 3, 5, 9, 4, 6, 8];
        $sum = 0;
        
        for ($i = 0; $i < 9; $i++) {
            $sum += ((int) $inn[$i]) * $weights[$i];
        }
        
        $checkDigit = $sum % 11;
        if ($checkDigit > 9) {
            $checkDigit = $checkDigit % 10;
        }
        
        return $checkDigit === (int) $inn[9];
    }

    /**
     * Get all TIN types supported by Russia.
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
     * Identify the TIN type for a given Russian TIN.
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
     * Get placeholder text.
     */
    public function getPlaceholder(): string
    {
        return '123456789012';
    }
} 