<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

/**
 * Hungary.
 */
final class Hungary extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'HU';

    /**
     * @var int
     */
    public const LENGTH = 10;

    /**
     * @var string
     */
    public const PATTERN = '8\d{9}';

    /**
     * @var string
     */
    public const MASK = '9999999999';

    protected function hasValidRule(string $tin): bool
    {
        $c10 = $this->digitAt($tin, 9);
        $sum = 0;

        for ($i = 0; 9 > $i; ++$i) {
            $c11 = (int) (substr($tin, $i, 1));
            $sum += $c11 * ($i + 1);
        }
        $remainderBy11 = $sum % 11;

        return $remainderBy11 === $c10;
    }

    public function getPlaceholder(): string
    {
        return '8071592153';
    }
}
