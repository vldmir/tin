<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

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
    public const PATTERN_1 = '\d{7}[a-wA-W]([a-iA-I]|W)';

    /**
     * @var string
     */
    public const PATTERN_2 = '\d{7}[a-wA-W]';

    /**
     * @var string
     */
    public const MASK = '9999999AA';

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

    private function letterToNumber(string $toConv): int
    {
        if ('W' === $toConv || 'w' === $toConv) {
            return 0;
        }

        return $this->getAlphabeticalPosition($toConv);
    }

    public function getPlaceholder(): string
    {
        return '1234567FA';
    }

    /**
     * Get all TIN types supported by Ireland.
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
}
