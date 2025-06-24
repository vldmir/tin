<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function array_key_exists;
use function in_array;
use function sprintf;

/**
 * Czech Republic.
 *
 * Source: https://github.com/czechphp/national-identification-number-validator
 */
final class CzechRepublic extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'CZ';

    /**
     * @var int
     */
    public const LENGTH_1 = 9;

    /**
     * @var int
     */
    public const LENGTH_2 = 10;

    /**
     * @var string
     */
    public const MASK = '999999/9999';

    // phpcs:disable

    /**
     * @var string
     */
    public const PATTERN = '^(?<year>\d{2})(?<month>\d{2})(?<day>\d{2})(?<slash>\/)?(?<sequence>\d{3})(?<modulo>\d{1})?$';

    // phpcs:enable

    /**
     * @var int
     */
    private const MODULO = 11;

    /**
     * @var int
     */
    private const MONTH_AFTER_2004 = 20;

    /**
     * @var int
     */
    private const MONTH_FEMALE = 50;

    /**
     * Returns a sample Czech Birth Number (Rodné číslo) in the correct format.
     *
     * @return string Example TIN for the Czech Republic.
     */
    public function getPlaceholder(): string
    {
        return '855230/3174';
    }

    /**
     * Returns an array describing the supported TIN types for the Czech Republic.
     *
     * @return array An array containing information about each supported TIN type, including code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'RC',
                'name' => 'Czech RC',
                'description' => 'Czech Birth Number (Rodné číslo)',
            ],
        ];
    }

    /**
     * Validates the date portion of a Czech TIN (Rodné číslo).
     *
     * Checks if the extracted month and day values from the TIN are within allowed ranges, accounting for gender and special month adjustments for births after 2004.
     *
     * @param string $tin The Czech TIN to validate.
     * @return bool True if the date portion is valid, false otherwise.
     */
    protected function hasValidDate(string $tin): bool
    {
        // If we reach this point, it means that it's already validated.
        preg_match(sprintf('/%s/', self::PATTERN), $tin, $matches);

        $hasModulo = array_key_exists('modulo', $matches) && '' !== $matches['modulo'];

        // range of months from 1 to 12
        $allowedMonths = array_merge(
            range(
                1,
                12
            ), // male
            range(
                1 + self::MONTH_FEMALE,
                12 + self::MONTH_FEMALE
            ) // female
        );

        // from year 2004 there can be people with +20 in their month number
        // without modulo check it would work for people born between 1904 and 19{last_two_digits_of_current_year} too
        if (true === $hasModulo && 4 <= $matches['year'] && date('y') >= $matches['year']) {
            $allowedMonths = array_merge(
                $allowedMonths,
                range(
                    1 + self::MONTH_AFTER_2004,
                    12 + self::MONTH_AFTER_2004
                ), // male
                range(
                    1 + self::MONTH_FEMALE + self::MONTH_AFTER_2004,
                    12 + self::MONTH_FEMALE + self::MONTH_AFTER_2004
                ) // female
            );
        }

        if (!in_array((int) $matches['month'], $allowedMonths, true)) {
            return false;
        }

        // day is between 1 and 31
        if (1 > $matches['day'] || 31 < $matches['day']) {
            return false;
        }

        return true;
    }

    protected function hasValidLength(string $tin): bool
    {
        return $this->isFollowLength1($tin) || $this->isFollowLength2($tin);
    }

    protected function hasValidRule(string $tin): bool
    {
        // If we reach this point, it means that it's already validated.
        preg_match(sprintf('/%s/', self::PATTERN), $tin, $matches);

        $hasModulo = array_key_exists('modulo', $matches) && '' !== $matches['modulo'];

        // after year 1953 everyone should have modulo
        // this validation does not work for people born since year 2000
        if (53 < $matches['year'] && false === $hasModulo) {
            return false;
        }

        // if there is no modulo then sequence can be between 001 and 999
        if (false === $hasModulo && 1 > $matches['sequence']) {
            return false;
        }

        // number's modulo should be 0
        if (true === $hasModulo) {
            $number = (int) ($matches['year'] . $matches['month'] . $matches['day'] . $matches['sequence']);
            $modulo = $number % self::MODULO;

            // from year 1954 to 1985 and sometimes even after that, modulo can be 10 which results in 0 as modulo
            if (10 === $modulo) {
                $modulo = 0;
            }

            if (((int) $matches['modulo']) !== $modulo) {
                return false;
            }
        }

        return true;
    }

    private function isFollowLength1(string $tin): bool
    {
        return $this->matchLength($tin, self::LENGTH_1);
    }

    /**
     * Checks if the TIN has a length equal to the defined LENGTH_2 constant (10 characters).
     *
     * @param string $tin The Tax Identification Number to check.
     * @return bool True if the TIN length is 10 characters, false otherwise.
     */
    private function isFollowLength2(string $tin): bool
    {
        return $this->matchLength($tin, self::LENGTH_2);
    }
}
