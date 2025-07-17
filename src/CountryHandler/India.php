<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function in_array;

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
     * @var int
     */
    public const LENGTH = 10;

    /**
     * @var string
     */
    public const MASK = 'AAAAA9999A';

    /**
     * PAN Pattern: AAAAA9999A (5 letters + 4 digits + 1 letter).
     *
     * @var string
     */
    public const PATTERN = '^[A-Z]{5}\d{4}[A-Z]$';

    /**
     * Returns a sample Indian PAN (Permanent Account Number) as a placeholder.
     *
     * @return string Example PAN value.
     */
    public function getPlaceholder(): string
    {
        return 'AFZPK7190K';
    }

    /**
     * Returns an array of supported Indian TIN types, including metadata for the Permanent Account Number (PAN).
     *
     * @return array An associative array describing the supported TIN types for India.
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
     * Checks if the provided TIN matches the Indian PAN format pattern.
     *
     * @param string $tin The TIN to validate.
     * @return bool True if the TIN matches the PAN regex pattern, false otherwise.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates additional structural rules for an Indian PAN (Permanent Account Number) beyond basic pattern matching.
     *
     * Checks that the fourth character represents a valid holder type and that the third character is a valid uppercase letter as required by PAN rules.
     *
     * @param string $tin The PAN to validate.
     * @return bool True if the PAN passes all rule-based checks; false otherwise.
     */
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
     * Returns the descriptive name for a PAN holder type letter.
     *
     * @param string $letter The fourth character of the PAN representing the holder type.
     * @return string The description of the holder type, or 'Unknown' if the letter is not recognized.
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
     * Checks if the provided letter is a valid PAN holder type.
     *
     * @param string $letter The fourth character of the PAN to validate.
     * @return bool True if the letter is a recognized PAN holder type, false otherwise.
     */
    private function isValidHolderType(string $letter): bool
    {
        $validTypes = ['A', 'B', 'C', 'F', 'G', 'H', 'L', 'J', 'P', 'T'];

        return in_array($letter, $validTypes, true);
    }

    /**
     * Checks if the third letter of a PAN is a single uppercase letter, as required by PAN rules.
     *
     * The validation does not verify correspondence with a surname or company name, only that the character is an uppercase letter.
     *
     * @param string $thirdLetter The third character of the PAN.
     * @param string $fourthLetter The fourth character of the PAN, indicating holder type.
     * @return bool True if the third letter is a single uppercase letter, false otherwise.
     */
    private function isValidThirdLetter(string $thirdLetter, string $fourthLetter): bool
    {
        // For individuals (fourth letter is P), third letter should be first letter of surname
        // For companies (fourth letter is C), third letter should be first letter of company name
        // Since we can't validate against actual names, we just check it's a letter
        return preg_match('/^[A-Z]$/', $thirdLetter) === 1;
    }
}
