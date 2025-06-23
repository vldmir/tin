<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

/**
 * India TIN validation.
 * Supports PAN (Permanent Account Number).
 */
final class India extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'IN';

    /**
     * PAN Pattern: AAAAA9999A (5 letters + 4 digits + 1 letter)
     * @var string
     */
    public const PATTERN = '^[A-Z]{5}\d{4}[A-Z]$';

    /**
     * @var int
     */
    public const LENGTH = 10;

    /**
     * @var string
     */
    public const MASK = 'AAAAA9999A';

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    protected function hasValidRule(string $tin): bool
    {
        // Extract components
        $firstThreeLetters = substr($tin, 0, 3);
        $fourthLetter = substr($tin, 3, 1);
        $fifthLetter = substr($tin, 4, 1);
        $digits = substr($tin, 5, 4);
        $lastLetter = substr($tin, 9, 1);
        
        // Validate fourth letter (type of holder)
        if (!$this->isValidHolderType($fourthLetter)) {
            return false;
        }
        
        // Validate third letter based on fourth letter
        if (!$this->isValidThirdLetter($firstThreeLetters[2], $fourthLetter)) {
            return false;
        }
        
        return true;
    }

    /**
     * Validate holder type (fourth letter).
     */
    private function isValidHolderType(string $letter): bool
    {
        $validTypes = ['A', 'B', 'C', 'F', 'G', 'H', 'L', 'J', 'P', 'T'];
        return in_array($letter, $validTypes);
    }

    /**
     * Validate third letter based on holder type.
     */
    private function isValidThirdLetter(string $thirdLetter, string $fourthLetter): bool
    {
        // For individuals (fourth letter is P), third letter should be first letter of surname
        // For companies (fourth letter is C), third letter should be first letter of company name
        // Since we can't validate against actual names, we just check it's a letter
        return preg_match('/^[A-Z]$/', $thirdLetter) === 1;
    }

    /**
     * Get all TIN types supported by India.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'PAN',
                'name' => 'Permanent Account Number',
                'description' => 'Indian permanent account number for tax purposes',
            ],
        ];
    }

    /**
     * Get holder type description.
     */
    private function getHolderTypeDescription(string $letter): string
    {
        $types = [
            'A' => 'Association of Persons (AOP)',
            'B' => 'Body of Individuals (BOI)',
            'C' => 'Company',
            'F' => 'Firm/Limited Liability Partnership',
            'G' => 'Government Agency',
            'H' => 'Hindu Undivided Family (HUF)',
            'L' => 'Local Authority',
            'J' => 'Artificial Juridical Person',
            'P' => 'Individual',
            'T' => 'Trust',
        ];
        
        return $types[$letter] ?? 'Unknown';
    }

    /**
     * Get placeholder text.
     */
    public function getPlaceholder(): string
    {
        return 'AFZPK7190K';
    }
} 