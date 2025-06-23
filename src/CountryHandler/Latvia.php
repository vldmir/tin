<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

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
    public const PATTERN = '[0-3]\d[0-1]\d{3}\d{5}';

    /**
     * @var string
     */
    public const MASK = '999999-99999';

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

    public function getPlaceholder(): string
    {
        return '161175-19997';
    }

    /**
     * Get all TIN types supported by Latvia.
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
}
