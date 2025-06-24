<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Poland.
 */
final class Poland extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'PL';

    /**
     * @var int
     */
    public const LENGTH_1 = 10;

    /**
     * @var int
     */
    public const LENGTH_2 = 11;

    /**
     * @var string
     */
    public const MASK = '99999999999';

    /**
     * @var string
     */
    public const PATTERN_1 = '\d{10}';

    /**
     * @var string
     */
    public const PATTERN_2 = '\d{11}';

    /**
     * Returns a sample valid PESEL number for Poland.
     *
     * @return string Example of a valid Polish TIN (PESEL).
     */
    public function getPlaceholder(): string
    {
        return '85071803874';
    }

    /**
     * Returns an array describing the TIN types supported in Poland.
     *
     * @return array An array containing information about the Polish PESEL TIN type, including its code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'PESEL',
                'name' => 'Polish PESEL',
                'description' => 'Polish National Identity Number (Powszechny Elektroniczny System Ewidencji Ludności)',
            ],
        ];
    }

    /**
     * Validates the date portion of an 11-digit Polish TIN (PESEL) based on encoded year, month, and day.
     *
     * Determines the correct century from the month value and checks if the extracted date is valid.
     *
     * @param string $tin The 11-digit TIN to validate.
     * @return bool True if the date portion is valid, false otherwise.
     */
    protected function hasValidDateWhenPattern2(string $tin): bool
    {
        $year = (int) (substr($tin, 0, 2));
        $month = (int) (substr($tin, 2, 2));
        $day = (int) (substr($tin, 4, 2));

        if (1 <= $month && 12 >= $month) {
            return checkdate($month, $day, 1900 + $year);
        }

        if (21 <= $month && 32 >= $month) {
            return checkdate($month - 20, $day, 2000 + $year);
        }

        if (41 <= $month && 52 >= $month) {
            return checkdate($month - 40, $day, 2100 + $year);
        }

        if (61 <= $month && 72 >= $month) {
            return checkdate($month - 60, $day, 2200 + $year);
        }

        return 81 <= $month && 92 >= $month && checkdate($month - 80, $day, 1800 + $year);
    }

    protected function hasValidLength(string $tin): bool
    {
        return $this->isFollowLength1($tin) || $this->isFollowLength2($tin);
    }

    protected function hasValidPattern(string $tin): bool
    {
        return $this->isFollowLength1AndPattern1($tin) || $this->isFollowLength2AndPattern2AndIsValidDate($tin);
    }

    protected function hasValidRule(string $tin): bool
    {
        return ($this->isFollowLength1($tin) && $this->isFollowRulePoland1($tin))
            || ($this->isFollowLength2($tin) && $this->isFollowRulePoland2($tin));
    }

    private function isFollowLength1(string $tin): bool
    {
        return $this->matchLength($tin, self::LENGTH_1);
    }

    private function isFollowLength1AndPattern1(string $tin): bool
    {
        return $this->isFollowLength1($tin) && $this->isFollowPattern1($tin);
    }

    private function isFollowLength2(string $tin): bool
    {
        return $this->matchLength($tin, self::LENGTH_2);
    }

    private function isFollowLength2AndPattern2AndIsValidDate(string $tin): bool
    {
        return $this->isFollowLength2($tin) && $this->isFollowPattern2($tin) && $this->hasValidDateWhenPattern2($tin);
    }

    private function isFollowPattern1(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN_1);
    }

    private function isFollowPattern2(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN_2);
    }

    private function isFollowRulePoland1(string $tin): bool
    {
        $map = [
            6,
            5,
            7,
            2,
            3,
            4,
            5,
            6,
            7,
        ];

        $sum = 0;

        foreach ($map as $key => $weight) {
            $sum += $this->digitAt($tin, $key) * $weight;
        }

        $remainderBy11 = $sum % 11;

        if (10 === $remainderBy11) {
            return false;
        }

        // @todo: Optimize that
        return $this->digitAt($tin, 9) === $remainderBy11;
    }

    /**
     * Validates the checksum of an 11-digit Polish TIN (PESEL) using the official algorithm.
     *
     * Calculates a weighted sum of the first 10 digits, applies modulo 10, and checks if the result matches the last digit of the TIN.
     *
     * @param string $tin The 11-digit TIN to validate.
     * @return bool True if the checksum is valid, false otherwise.
     */
    private function isFollowRulePoland2(string $tin): bool
    {
        $map = [
            1,
            3,
            7,
            9,
            1,
            3,
            7,
            9,
            1,
            3,
        ];

        $sum = 0;

        foreach ($map as $key => $weight) {
            $sum += $this->digitAt($tin, $key) * $weight;
        }

        $lastDigit = $sum % 10;

        return 10 - $lastDigit === $this->digitAt($tin, 10);
    }
}
