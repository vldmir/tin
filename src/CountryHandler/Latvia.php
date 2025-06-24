<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Latvia.
 */
final class Latvia extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'LV';

    /**
     * @var int
     */
    public const LENGTH = 11;

    /**
     * @var string
     */
    public const MASK = '999999-99999';

    /**
     * @var string
     */
    public const PATTERN = '[0-3]\d[0-1]\d{3}\d{5}';

    /**
     * Returns a sample Latvian TIN in the correct format.
     *
     * @return string Example TIN string for Latvia.
     */
    public function getPlaceholder(): string
    {
        return '161175-19997';
    }

    /**
     * Returns an array of supported Latvian TIN types with their codes, names, and descriptions.
     *
     * @return array An associative array of TIN types for Latvia.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'PK',
                'name' => 'Latvian PK',
                'description' => 'Latvian Personal Code (Personas kods)',
            ],
        ];
    }

    /**
     * Checks if the date portion of a Latvian TIN is valid.
     *
     * Returns true if the day component is '32', or if the extracted day, month, and year form a valid date in both the 1900s and 2000s.
     *
     * @param string $tin The Latvian TIN to validate.
     * @return bool True if the date is valid according to Latvian TIN rules, false otherwise.
     */
    protected function hasValidDate(string $tin): bool
    {
        $c1c2 = substr($tin, 0, 2);

        if ('32' === $c1c2) {
            return true;
        }
        $day = (int) $c1c2;
        $month = (int) (substr($tin, 2, 2));
        $year = (int) (substr($tin, 4, 2));

        $y1 = checkdate($month, $day, 1900 + $year);
        $y2 = checkdate($month, $day, 2000 + $year);

        return $y1 && $y2;
    }
}
