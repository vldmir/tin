<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Romania.
 */
final class Romania extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'RO';

    /**
     * @var int
     */
    public const LENGTH = 13;

    /**
     * @var string
     */
    public const MASK = '9999999999999';

    /**
     * @var string
     */
    public const PATTERN = '[1-8]\d{2}[0-1]\d[0-3]\d{6}';

    /**
     * Returns a sample Romanian TIN (CNP) as a placeholder string.
     *
     * @return string Example TIN value for Romania.
     */
    public function getPlaceholder(): string
    {
        return '1630615123457';
    }

    /**
     * Returns an array of supported Romanian TIN types with their codes, names, and descriptions.
     *
     * @return array An associative array describing each supported TIN type for Romania.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'CNP',
                'name' => 'Romanian CNP',
                'description' => 'Romanian Personal Numerical Code (Codul Numeric Personal)',
            ],
        ];
    }

    /**
     * Checks if the date encoded in the TIN is valid for both the 1900 and 2000 centuries.
     *
     * Extracts the year, month, and day from the TIN and verifies that the date is valid when interpreted as both 1900+year and 2000+year.
     *
     * @param string $tin The Romanian TIN to validate.
     * @return bool True if the date is valid in both centuries, false otherwise.
     */
    protected function hasValidDate(string $tin): bool
    {
        $year = (int) (substr($tin, 1, 2));
        $month = (int) (substr($tin, 3, 2));
        $day = (int) (substr($tin, 5, 2));

        $y1 = checkdate($month, $day, 1900 + $year);
        $y2 = checkdate($month, $day, 2000 + $year);

        return $y1 && $y2;
    }

    /**
     * Checks if the given Romanian TIN satisfies structural rules for validity.
     *
     * Validates that the first digit is not zero and that the county code is either 51, 52, or less than or equal to 47.
     *
     * @param string $tin The Romanian TIN to validate.
     * @return bool True if the TIN passes the rule checks, false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $c1 = (int) ($tin[0]);

        if (0 === $c1) {
            return false;
        }

        $county = (int) (substr($tin, 7, 2));

        if (47 < $county && 51 !== $county && 52 !== $county) {
            return false;
        }

        return true;
    }
}
