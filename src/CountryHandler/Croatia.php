<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

/**
 * Croatia.
 */
final class Croatia extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'HR';

    /**
     * @var int
     */
    public const LENGTH = 11;

    /**
     * @var string
     */
    public const PATTERN = '\d{11}';

    /**
     * @var string
     */
    public const MASK = '99999999999';

    protected function hasValidRule(string $tin): bool
    {
        $rest = 0;
        $sum = $this->digitAt($tin, 0) + 10;

        for ($i = 1; 11 > $i; ++$i) {
            $rest = $sum % 10;
            $rest = ((0 === $rest) ? 10 : $rest) * 2 % 11;
            $sum = $rest + $this->digitAt($tin, $i);
        }
        $diff = 11 - $rest;
        $lastDigit = $this->digitAt($tin, 10);

        return (1 === $rest && 0 === $lastDigit) || $lastDigit === $diff;
    }

    public function getPlaceholder(): string
    {
        return '94577403194';
    }

    /**
     * Get all TIN types supported by Croatia.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'OIB',
                'name' => 'Croatian OIB',
                'description' => 'Croatian Personal Identification Number (Osobni identifikacijski broj)',
            ],
        ];
    }
}
