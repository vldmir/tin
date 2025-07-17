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

    public function getPlaceholder(): string
    {
        return 'AB123456C';
    }

    /**
     * Get all TIN types supported by United Kingdom.
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
     * Identify the TIN type for a given UK TIN.
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

    private function isFollowStructureSubRule2(string $tin): bool
    {
        $c1c2 = substr($tin, 0, 2);

        return 'GB' !== $c1c2 && 'NK' !== $c1c2 && 'TN' !== $c1c2 && 'ZZ' !== $c1c2;
    }
}
