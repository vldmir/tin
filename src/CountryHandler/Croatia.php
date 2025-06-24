<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

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
    public const MASK = '99999999999';

    /**
     * @var string
     */
    public const PATTERN = '\d{11}';

    /**
     * Returns a sample Croatian TIN (OIB) for placeholder purposes.
     *
     * @return string Example Croatian TIN.
     */
    public function getPlaceholder(): string
    {
        return '94577403194';
    }

    /**
     * Returns an array describing the supported Croatian TIN types.
     *
     * @return array An array containing metadata for each supported TIN type, including code, name, and description.
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

    /**
     * Validates a Croatian TIN (OIB) using the official modulus 11 algorithm.
     *
     * @param string $tin The TIN to validate.
     * @return bool True if the TIN is valid according to Croatian rules, false otherwise.
     */
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
}
