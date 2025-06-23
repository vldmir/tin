<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

use const STR_PAD_LEFT;

/**
 * Spain.
 */
final class Spain extends CountryHandler
{
    /**
     * @var string
     */
    public const CHECKSUM_LETTER = 'KLMNPQRSW';

    /**
     * @var string
     */
    public const CONTROL_1 = 'TRWAGMYFPDXBNJZSQVHLCKE';

    /**
     * @var string
     */
    public const CONTROL_2 = 'JABCDEFGHI';

    /**
     * @var string
     */
    public const COUNTRYCODE = 'ES';

    /**
     * @var int
     */
    public const LENGTH = 9;

    /**
     * @var array<string>
     */
    public const NIE = ['X', 'Y', 'Z'];

    /**
     * Spanish Natural Persons: DNI
     * Foreigners with NIE.
     *
     * @var string
     */
    public const PATTERN_1 = '(^[XYZ\d]\d{7})([' . self::CONTROL_1 . ']$)';

    /**
     * Non-resident Spaniards without DNI
     * Resident Spaniards under 14 without DNI
     * Foreigners without NIE
     * Legal entities (companies, organizations, public entities, ...).
     *
     * @var string
     */
    public const PATTERN_2 = '(^[ABCDEFGHJKLMNPQRSUVW])(\d{7})([' . self::CONTROL_2 . '\d]$)';

    /**
     * @var string
     */
    public const MASK = '99999999A';

    protected function hasValidPattern(string $tin): bool
    {
        return $this->isFollowPattern1($tin) || $this->isFollowPattern2($tin);
    }

    protected function hasValidRule(string $tin): bool
    {
        return $this->isFollowRule1($tin) || $this->isFollowRule2($tin);
    }

    protected function normalizeTin(string $tin): string
    {
        $tin = parent::normalizeTin($tin);

        return str_pad($tin, self::LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Return checksum char for Spanish TIN.
     *
     * @param string $tin
     * The TIN without Country indicative ('ES')
     * @param null|bool $digit
     * Optional: for Non-Natural Persons TIN forces return checksum char as digit 0-9
     *
     * @return null|string
     * Return checksum char or null on failure
     */
    private function getChecksum(string $tin, ?bool $digit = null): ? string
    {
        // Natural Persons with DNI or NIE
        if (1 === preg_match('~' . self::PATTERN_1 . '?~', strtoupper($tin), $tinParts)) {
            $tinNumber = (int) str_replace(self::NIE, array_keys(self::NIE), $tinParts[1]);

            return substr(self::CONTROL_1, $tinNumber % 23, 1);
        }

        // Natural Persons without DNI or NIE and Non-Natural Persons
        if (1 === preg_match('~' . self::PATTERN_2 . '?~', strtoupper($tin), $tinParts)) {
            $checksum = 0;

            foreach (str_split($tinParts[2]) as $pos => $val) {
                $checksum += array_sum(str_split((string) ((int) $val * (2 - ($pos % 2)))));
            }

            $checksum1 = (string) ((10 - ($checksum % 10)) % 10);
            $checksum2 = substr(self::CONTROL_2, (int) $checksum1, 1);

            if (null === $digit) {
                $digit = false === strpos(self::CHECKSUM_LETTER, $tinParts[1]);
            }

            return $digit ? $checksum1 : $checksum2;
        }

        return null;
    }

    private function isFollowPattern1(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN_1);
    }

    private function isFollowPattern2(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN_2);
    }

    private function isFollowRule1(string $tin): bool
    {
        if (1 !== preg_match('~' . self::PATTERN_1 . '~', strtoupper($tin), $tinParts)) {
            return false;
        }

        [, $tinNumber, $tinChecksum] = $tinParts;

        return $this->getChecksum($tinNumber) === $tinChecksum;
    }

    private function isFollowRule2(string $tin): bool
    {
        if (1 !== preg_match('~' . self::PATTERN_2 . '~', strtoupper($tin), $tinParts)) {
            return false;
        }

        [,$tinFirstLetter , $tinNumber, $tinChecksum] = $tinParts;

        $tinNumber = $tinFirstLetter . $tinNumber;
        $digit = (false === strpos(self::CONTROL_2, $tinChecksum));

        return $this->getChecksum($tinNumber, $digit) === $tinChecksum;
    }

    public function getPlaceholder(): string
    {
        return '12345678Z';
    }

    /**
     * Get all TIN types supported by Spain.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'DNI',
                'name' => 'Documento Nacional de Identidad',
                'description' => 'Spanish Natural Persons ID',
            ],
            2 => [
                'code' => 'NIE',
                'name' => 'Número de Identidad de Extranjero',
                'description' => 'Foreigners Identification Number',
            ],
            3 => [
                'code' => 'CIF',
                'name' => 'Código de Identificación Fiscal',
                'description' => 'Tax Identification Code for Legal Entities',
            ],
        ];
    }

    /**
     * Identify the TIN type for a given Spanish TIN.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);
        
        // Pattern 1: DNI or NIE
        if ($this->isFollowPattern1($normalizedTin)) {
            // Check if it starts with X, Y, or Z (NIE)
            if (in_array($normalizedTin[0], self::NIE, true)) {
                return $this->getTinTypes()[2]; // NIE
            }
            // Otherwise it's a DNI
            return $this->getTinTypes()[1]; // DNI
        }
        
        // Pattern 2: CIF (Legal entities)
        if ($this->isFollowPattern2($normalizedTin)) {
            return $this->getTinTypes()[3]; // CIF
        }
        
        return null;
    }
}
