<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

/**
 * France.
 */
final class France extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'FR';

    /**
     * @var int
     */
    public const LENGTH = 13;

    /**
     * @var string
     */
    public const PATTERN = '[0-3]\d{12}';

    /**
     * @var string
     */
    public const MASK = '9 99 99 99 999 999';

    protected function hasValidRule(string $tin): bool
    {
        $number = (int) (substr($tin, 0, 10));

        $remainderBy511 = $number % 511;

        $checkDigits = 100 > $remainderBy511 ?
            10 > $remainderBy511 ? (int) (substr($tin, 12, 13)) : (int) (substr($tin, 11, 13)) :
            (int) (substr($tin, 10, 13));

        return $remainderBy511 === $checkDigits;
    }

    public function getPlaceholder(): string
    {
        return '1 23 45 67 890 123';
    }
}
