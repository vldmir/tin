<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use const STR_PAD_RIGHT;

/**
 * United Kingdom.
 */
final class UnitedKingdom extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'UK';

    /**
     * @var int
     */
    public const LENGTH_1 = 10;

    /**
     * @var int
     */
    public const LENGTH_2 = 9;

    /**
     * @var string
     */
    public const MASK = 'AA999999A';

    /**
     * @var string
     */
    public const PATTERN_1 = '\d{10}';

    /**
     * @var string
     */
    public const PATTERN_2 = '[a-ceg-hj-pr-tw-zA-CEG-HJ-PR-TW-Z][a-ceg-hj-npr-tw-zA-CEG-HJ-NPR-TW-Z]\d{6}[abcdABCD ]';

    /**
     * Returns a sample UK Tax Identification Number (TIN) in the standard format.
     *
     * @return string Example TIN string ('AB123456C').
     */
    public function getPlaceholder(): string
    {
        return 'AB123456C';
    }

    /**
     * Returns an array of supported UK TIN types, including UTR and NINO, with their codes, names, and descriptions.
     *
     * @return array An associative array describing the Unique Taxpayer Reference (UTR) and National Insurance Number (NINO) TIN types.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'UTR',
                'name' => 'Unique Taxpayer Reference',
                'description' => '10-digit tax reference number',
            ],
            2 => [
                'code' => 'NINO',
                'name' => 'National Insurance Number',
                'description' => 'National Insurance number for individuals',
            ],
        ];
    }

    /**
     * Determines the type of a UK Tax Identification Number (TIN) as either UTR or NINO.
     *
     * Returns an array describing the TIN type if the input matches the format for a Unique Taxpayer Reference (UTR) or a National Insurance Number (NINO), or null if the TIN does not match either type.
     *
     * @param string $tin The TIN to be identified.
     * @return array|null The TIN type information array if identified, or null if no match is found.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);
        $paddedTin = str_pad($normalizedTin, 9, ' ', STR_PAD_RIGHT);

        // Pattern 1: 10-digit UTR
        if ($this->isFollowLength1($paddedTin) && $this->isFollowPattern1($paddedTin)) {
            return $this->getTinTypes()[1]; // UTR
        }

        // Pattern 2: National Insurance Number
        if ($this->isFollowLength2($paddedTin) && $this->isFollowPattern2($paddedTin)) {
            return $this->getTinTypes()[2]; // NINO
        }

        return null;
    }

    /**
     * Checks if the provided TIN has a valid length for UK TIN types.
     *
     * Pads the TIN to length 9 and returns true if it matches the required length for either UTR (10 digits) or NINO (9 characters).
     *
     * @param string $tin The Tax Identification Number to check.
     * @return bool True if the TIN has a valid length, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        $tin = str_pad($tin, 9, ' ', STR_PAD_RIGHT);

        return $this->isFollowLength1($tin) || $this->isFollowLength2($tin);
    }

    protected function hasValidPattern(string $tin): bool
    {
        $tin = str_pad($tin, 9, ' ', STR_PAD_RIGHT);

        if ($this->isFollowLength1($tin) && !$this->isFollowPattern1($tin)) {
            return false;
        }

        if ($this->isFollowLength2($tin) && !$this->isFollowPattern2($tin)) {
            return false;
        }

        return true;
    }

    private function isFollowLength1(string $tin): bool
    {
        return $this->matchLength($tin, self::LENGTH_1);
    }

    private function isFollowLength2(string $tin): bool
    {
        return $this->matchLength($tin, self::LENGTH_2);
    }

    private function isFollowPattern1(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN_1);
    }

    private function isFollowPattern2(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN_2) && $this->isFollowStructureSubRule2($tin);
    }

    /**
     * Checks if the first two characters of the TIN do not match any disallowed prefixes for NINO validation.
     *
     * Returns true if the TIN does not start with 'GB', 'NK', 'TN', or 'ZZ'; otherwise, returns false.
     *
     * @param string $tin The Tax Identification Number to check.
     * @return bool True if the TIN passes the structural sub-rule, false otherwise.
     */
    private function isFollowStructureSubRule2(string $tin): bool
    {
        $c1c2 = substr($tin, 0, 2);

        return 'GB' !== $c1c2 && 'NK' !== $c1c2 && 'TN' !== $c1c2 && 'ZZ' !== $c1c2;
    }
}
