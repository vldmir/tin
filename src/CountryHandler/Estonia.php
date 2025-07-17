<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Estonia.
 */
final class Estonia extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'EE';

    /**
     * @var int
     */
    public const LENGTH = 11;

    /**
     * @var string
     */
    public const MASK = '99999999999';

    /**
     * @var string
     */
    public const PATTERN = '[1-6]\d{2}[0-1]\d[0-3]\d{5}';

    /**
     * Returns a sample valid Estonian TIN for use as a placeholder.
     *
     * @return string Example Estonian TIN.
     */
    public function getPlaceholder(): string
    {
        return '37605030299';
    }

    /**
     * Returns an array describing the supported Estonian TIN types.
     *
     * @return array An array containing information about the Estonian Personal Identification Code (Isikukood).
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'IK',
                'name' => 'Estonian IK',
                'description' => 'Estonian Personal Identification Code (Isikukood)',
            ],
        ];
    }

    /**
     * Checks if the date portion of the provided Estonian TIN represents a valid calendar date in either the 1900s or 2000s.
     *
     * @param string $tin The Estonian TIN to validate.
     * @return bool True if the extracted date is valid for either century; otherwise, false.
     */
    protected function hasValidDate(string $tin): bool
    {
        $year = (int) (substr($tin, 1, 2));
        $month = (int) (substr($tin, 3, 2));
        $day = (int) (substr($tin, 5, 2));

        $d1 = checkdate($month, $day, 1900 + $year);
        $d2 = checkdate($month, $day, 2000 + $year);

        return $d1 || $d2;
    }

    protected function hasValidRule(string $tin): bool
    {
        $range = (int) (substr($tin, 7, 3));

        if (false === (0 < $range && 711 > $range)) {
            return false;
        }

        $c1 = $this->digitAt($tin, 0);
        $c2 = $this->digitAt($tin, 1);
        $c3 = $this->digitAt($tin, 2);
        $c4 = $this->digitAt($tin, 3);
        $c5 = $this->digitAt($tin, 4);
        $c6 = $this->digitAt($tin, 5);
        $c7 = $this->digitAt($tin, 6);
        $c8 = $this->digitAt($tin, 7);
        $c9 = $this->digitAt($tin, 8);
        $c10 = $this->digitAt($tin, 9);
        $c11 = $this->digitAt($tin, 10);
        $sum = $c1 + $c2 * 2 + $c3 * 3 + $c4 * 4 + $c5 * 5 + $c6 * 6 + $c7 * 7 + $c8 * 8 + $c9 * 9 + $c10;
        $remainderBy11 = $sum % 11;

        return (10 > $remainderBy11 && $remainderBy11 === $c11)
            || (10 === $remainderBy11 && $this->isFollowEstoniaRulePart2($tin));
    }

    /**
     * Validates the Estonian TIN checksum using the secondary rule when the primary checksum yields a remainder of 10.
     *
     * Applies a specific weighted sum and modulo 11 operation to the TIN digits. Returns true if the result matches the last digit or, if the remainder is 10, the last digit is zero.
     *
     * @param string $tin The Estonian TIN to validate.
     * @return bool True if the TIN passes the secondary checksum rule, false otherwise.
     */
    private function isFollowEstoniaRulePart2(string $tin): bool
    {
        $c1 = $this->digitAt($tin, 0);
        $c2 = $this->digitAt($tin, 1);
        $c3 = $this->digitAt($tin, 2);
        $c4 = $this->digitAt($tin, 3);
        $c5 = $this->digitAt($tin, 4);
        $c6 = $this->digitAt($tin, 5);
        $c7 = $this->digitAt($tin, 6);
        $c8 = $this->digitAt($tin, 7);
        $c9 = $this->digitAt($tin, 8);
        $c10 = $this->digitAt($tin, 9);
        $c11 = $this->digitAt($tin, 10);
        $sum = $c1 * 3 + $c2 * 4 + $c3 * 5 + $c4 * 6 + $c5 * 7 + $c6 * 8 + $c7 * 9 + $c8 + $c9 * 2 + $c10 * 3;
        $remainderBy11 = $sum % 11;

        return (10 > $remainderBy11 && $remainderBy11 === $c11) || (10 === $remainderBy11 && 0 === $c11);
    }
}
