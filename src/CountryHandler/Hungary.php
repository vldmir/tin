<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

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
    public const MASK = '9999999999';

    /**
     * @var string
     */
    public const PATTERN = '8\d{9}';

    /**
     * Returns a placeholder string for a Hungarian Tax Identification Number (TIN).
     *
     * @return string The placeholder value for a Hungarian TIN.
     */
    public function getPlaceholder(): string
    {
        return '8123456789';
    }

    /**
     * Returns an array describing the supported Hungarian TIN types.
     *
     * @return array An array of TIN type metadata, including code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'ANUM',
                'name' => 'Hungarian ANUM',
                'description' => 'Hungarian Tax Number (Adóazonosító szám)',
            ],
        ];
    }

    /**
     * Validates a Hungarian TIN using the official checksum algorithm.
     *
     * Calculates a weighted sum of the first nine digits of the TIN, applies modulo 11, and checks if the result matches the tenth digit.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN passes the checksum validation; otherwise, false.
     */
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
}
