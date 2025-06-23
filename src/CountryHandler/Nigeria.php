<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

/**
 * Nigeria TIN validation.
 * Supports TIN (Tax Identification Number) - 10 digits JTB format.
 */
final class Nigeria extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'NG';

    /**
     * TIN Pattern: 10 digits (can be formatted as 9999999999)
     * @var string
     */
    public const PATTERN = '^\d{10}$';

    /**
     * @var int
     */
    public const LENGTH = 10;

    /**
     * @var string
     */
    public const MASK = '9999999999';

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    protected function hasValidRule(string $tin): bool
    {
        // Check if all digits are zeros (invalid)
        if (preg_match('/^0+$/', $tin)) {
            return false;
        }
        
        // Check if all digits are the same (invalid)
        if (preg_match('/^(\d)\1{9}$/', $tin)) {
            return false;
        }
        
        // First digit should not be 0
        if ($tin[0] === '0') {
            return false;
        }
        
        return true;
    }

    /**
     * Get placeholder text.
     */
    public function getPlaceholder(): string
    {
        return '1234567890';
    }

    /**
     * Get all TIN types supported by Nigeria.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'TIN',
                'name' => 'Nigerian TIN',
                'description' => 'Nigerian Tax Identification Number',
            ],
        ];
    }
} 