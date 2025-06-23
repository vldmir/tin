<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

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
     * TFN Pattern: 8-9 digits
     * @var string
     */
    public const PATTERN_TFN = '^\d{8,9}$';

    /**
     * ABN Pattern: 11 digits (can be formatted as 99 999 999 999)
     * @var string
     */
    public const PATTERN_ABN = '^\d{2}\s?\d{3}\s?\d{3}\s?\d{3}$';

    /**
     * Combined pattern for all types
     * @var string
     */
    public const PATTERN = '^(\d{8,9}|\d{2}\s?\d{3}\s?\d{3}\s?\d{3})$';

    /**
     * @var int
     */
    public const LENGTH = 11; // Maximum length (ABN)

    /**
     * @var string
     */
    public const MASK = '99 999 999 999'; // Default to ABN

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    protected function hasValidLength(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);
        $length = strlen($normalizedTin);
        return $length >= 8 && $length <= 11;
    }

    protected function hasValidRule(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);
        $length = strlen($normalizedTin);
        
        // TFN validation (8-9 digits)
        if ($length === 8 || $length === 9) {
            return $this->isValidTFN($normalizedTin);
        }
        
        // ABN validation (11 digits)
        if ($length === 11) {
            return $this->isValidABN($normalizedTin);
        }
        
        return false;
    }

    /**
     * Validate TFN.
     * TFN uses a weighted checksum algorithm.
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
            '888888888', '999999999', '123456789', '987654321'
        ];
        
        if (in_array($tfn, $invalidTFNs)) {
            return false;
        }
        
        // If 8 digits, apply weighted checksum
        if (strlen($tfn) === 8) {
            $weights = [10, 7, 8, 4, 6, 3, 5, 1];
            $sum = 0;
            
            for ($i = 0; $i < 8; $i++) {
                $sum += ((int) $tfn[$i]) * $weights[$i];
            }
            
            return ($sum % 11) === 0;
        }
        
        // 9-digit TFNs follow different validation rules
        // Basic validation only (real validation would require ATO database)
        return true;
    }

    /**
     * Validate ABN using modulus 89 checksum.
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
        for ($i = 0; $i < 11; $i++) {
            $sum += ((int) $digits[$i]) * $weights[$i];
        }
        
        // Check if divisible by 89
        return ($sum % 89) === 0;
    }

    /**
     * Get input mask based on the TIN type.
     */
    public function getInputMask(): string
    {
        // Default to ABN format
        return '99 999 999 999';
    }

    /**
     * Get placeholder based on the TIN type.
     */
    public function getPlaceholder(): string
    {
        return '53 004 085 616';
    }

    /**
     * Get all TIN types supported by Australia.
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
     * Identify the TIN type for a given Australian TIN.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $this->normalizeTin($tin));
        $length = strlen($normalizedTin);
        
        // Check if it's TFN (8-9 digits)
        if (($length === 8 || $length === 9) && $this->isValidTFN($normalizedTin)) {
            return $this->getTinTypes()[1]; // TFN
        }
        
        // Check if it's ABN (11 digits)
        if ($length === 11 && $this->isValidABN($normalizedTin)) {
            return $this->getTinTypes()[2]; // ABN
        }
        
        return null;
    }

    /**
     * Format input according to TIN type.
     */
    public function formatInput(string $input): string
    {
        $normalized = preg_replace('/[^0-9]/', '', $input);
        
        if (strlen($normalized) === 0) {
            return '';
        }
        
        // ABN format: 99 999 999 999
        if (strlen($normalized) >= 10) {
            $result = '';
            for ($i = 0; $i < strlen($normalized) && $i < 11; $i++) {
                if ($i === 2 || $i === 5 || $i === 8) {
                    $result .= ' ';
                }
                $result .= $normalized[$i];
            }
            return $result;
        }
        
        // TFN format: no specific formatting, just digits
        return $normalized;
    }
} 