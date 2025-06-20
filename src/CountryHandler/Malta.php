<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

use function in_array;

/**
 * Malta.
 */
final class Malta extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'MT';

    /**
     * @var int
     */
    public const LENGTH = 8;

    /**
     * @var string
     */
    public const PATTERN = '\d{8}';

    /**
     * @var string
     */
    public const MASK = '99999999';

    protected function hasValidRule(string $tin): bool
    {
        $valid = ['M', 'G', 'A', 'P', 'L', 'H', 'B', 'Z'];

        return in_array($tin[7], $valid, true);
    }

    public function getPlaceholder(): string
    {
        return '12345678';
    }
}
