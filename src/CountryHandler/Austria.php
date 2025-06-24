<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Austria.
 */
final class Austria extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'AT';

    /**
     * @var int
     */
    public const LENGTH = 9;

    /**
     * @var string
     */
    public const MASK = '999999999';

    /**
     * @var string
     */
    public const PATTERN = '\d{9}';

    /**
     * Returns a sample Austrian TIN placeholder in the standard format.
     *
     * @return string Example placeholder for an Austrian Tax Identification Number.
     */
    public function getPlaceholder(): string
    {
        return '12 310170';
    }

    /**
     * Returns an array of supported Austrian TIN types with their codes, names, and descriptions.
     *
     * @return array An array describing the Austrian Tax Identification Number (Steuernummer).
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'TIN',
                'name' => 'Austrian TIN',
                'description' => 'Austrian Tax Identification Number (Steuernummer)',
            ],
        ];
    }

    /**
     * Validates an Austrian TIN using the country-specific checksum algorithm.
     *
     * The method checks if the provided TIN's last digit matches the calculated check digit based on weighted sums of its digits.
     *
     * @param string $tin The Austrian TIN to validate.
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

        $sum = array_sum([
            $c1,
            $c3,
            $c5,
            $c7,
            $this->digitsSum($c2 * 2),
            $this->digitsSum($c4 * 2),
            $this->digitsSum($c6 * 2),
            $this->digitsSum($c8 * 2),
        ]);

        $check = $this->getLastDigit(100 - $sum);

        return $c9 === $check;
    }
}
