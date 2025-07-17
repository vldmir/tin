<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * Ireland.
 */
final class Ireland extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'IE';

    /**
     * @var int
     */
    public const LENGTH_1 = 9;

    /**
     * @var int
     */
    public const LENGTH_2 = 8;

    /**
     * @var string
     */
    public const MASK = '9999999AA';

    /**
     * @var string
     */
    public const PATTERN_1 = '\d{7}[a-wA-W]([a-iA-I]|W)';

    /**
     * @var string
     */
    public const PATTERN_2 = '\d{7}[a-wA-W]';

    /**
     * Returns a sample Irish Personal Public Service Number (PPS) in the correct format.
     *
     * @return string Example PPS number.
     */
    public function getPlaceholder(): string
    {
        return '1234567FA';
    }

    /**
     * Returns an array of supported Irish TIN types.
     *
     * Each TIN type includes a code, name, and description. Currently, only the Irish Personal Public Service Number (PPS) is supported.
     *
     * @return array An array of TIN type definitions for Ireland.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'PPS',
                'name' => 'Irish PPS',
                'description' => 'Irish Personal Public Service Number',
            ],
        ];
    }

    /**
     * Checks if the provided TIN has a valid length for Irish TINs.
     *
     * Returns true if the TIN is either 8 or 9 characters long.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN length is valid, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        return $this->isFollowLength1($tin) || $this->isFollowLength2($tin);
    }

    protected function hasValidPattern(string $tin): bool
    {
        if ($this->isFollowLength1($tin) && !$this->isFollowPattern1($tin)) {
            return false;
        }

        return !($this->isFollowLength2($tin) && !$this->isFollowPattern2($tin));
    }

    protected function hasValidRule(string $tin): bool
    {
        $c1 = $this->digitAt($tin, 0);
        $c2 = $this->digitAt($tin, 1);
        $c3 = $this->digitAt($tin, 2);
        $c4 = $this->digitAt($tin, 3);
        $c5 = $this->digitAt($tin, 4);
        $c6 = $this->digitAt($tin, 5);
        $c7 = $this->digitAt($tin, 6);
        $c9 = (9 <= strlen($tin)) ? $this->letterToNumber($tin[8]) : 0;
        $c8 = $tin[7];
        $sum = $c9 * 9 + $c1 * 8 + $c2 * 7 + $c3 * 6 + $c4 * 5 + $c5 * 4 + $c6 * 3 + $c7 * 2;
        $remainderBy23 = $sum % 23;

        if (0 !== $remainderBy23) {
            return $this->getAlphabeticalPosition($c8) === $remainderBy23;
        }

        return 'W' === $c8 || 'w' === $c8;
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
        return $this->matchPattern($tin, self::PATTERN_2);
    }

    /**
     * Converts a letter to its corresponding number for checksum calculation.
     *
     * Returns 0 if the letter is 'W' or 'w'; otherwise, returns the alphabetical position of the letter.
     *
     * @param string $toConv The letter to convert.
     * @return int The numeric value used in the checksum.
     */
    private function letterToNumber(string $toConv): int
    {
        if ('W' === $toConv || 'w' === $toConv) {
            return 0;
        }

        return $this->getAlphabeticalPosition($toConv);
    }
}
