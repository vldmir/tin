<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Bulgaria.
 */
final class Bulgaria extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'BG';

    /**
     * @var int
     */
    public const LENGTH = 10;

    /**
     * @var string
     */
    public const MASK = '9999999999';

    /**
     * @var string
     */
    public const PATTERN = '\d{10}';

    /**
     * Returns a sample Bulgarian TIN (EGN) for use as a placeholder.
     *
     * @return string Example TIN string.
     */
    public function getPlaceholder(): string
    {
        return '7523169263';
    }

    /**
     * Returns an array of supported Bulgarian TIN types with their codes, names, and descriptions.
     *
     * @return array An associative array describing each supported TIN type.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'EGN',
                'name' => 'Bulgarian EGN',
                'description' => 'Bulgarian Unique Civil Number (Edinen Grazhdanski Nomer)',
            ],
        ];
    }

    /**
     * Checks if the date portion of a Bulgarian TIN represents a valid calendar date.
     *
     * The method interprets the first six digits of the TIN as a date, adjusting the year and month based on Bulgarian EGN encoding rules.
     *
     * @param string $tin The Bulgarian TIN to validate.
     * @return bool True if the extracted date is valid, false otherwise.
     */
    protected function hasValidDate(string $tin): bool
    {
        $year = (int) (substr($tin, 0, 2));
        $month = (int) (substr($tin, 2, 2));
        $day = (int) (substr($tin, 4, 2));

        if (21 <= $month && 32 >= $month) {
            return checkdate($month - 20, $day, 1800 + $year);
        }

        if (41 <= $month && 52 >= $month) {
            return checkdate($month - 40, $day, 2000 + $year);
        }

        return checkdate($month, $day, 1900 + $year);
    }

    /**
     * Validates the checksum of a Bulgarian TIN (EGN) according to official rules.
     *
     * The method calculates a weighted sum of the first nine digits, applies modulo 11, and checks if the result matches the last digit, with special handling if the remainder is 10.
     *
     * @param string $tin The Bulgarian TIN to validate.
     * @return bool True if the TIN passes the checksum validation; otherwise, false.
     */
    protected function hasValidRule(string $tin): bool
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
        $sum = $c1 * 2 + $c2 * 4 + $c3 * 8 + $c4 * 5 + $c5 * 10 + $c6 * 9 + $c7 * 7 + $c8 * 3 + $c9 * 6;
        $remainderBy11 = $sum % 11;

        if (10 === $remainderBy11) {
            return 0 === $c10;
        }

        return $remainderBy11 === $c10;
    }
}
