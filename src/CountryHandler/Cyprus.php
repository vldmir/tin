<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function ord;

/**
 * Cyprus.
 */
final class Cyprus extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'CY';

    /**
     * @var int
     */
    public const LENGTH = 9;

    /**
     * @var string
     */
    public const MASK = '99999999A';

    /**
     * @var string
     */
    public const PATTERN = '\d{8}[a-zA-Z]';

    /**
     * Returns a sample Cyprus Tax Identification Number (TIN) in the correct format.
     *
     * @return string Example TIN value for Cyprus.
     */
    public function getPlaceholder(): string
    {
        return '12345678L';
    }

    /**
     * Returns an array describing the supported Tax Identification Number (TIN) types for Cyprus.
     *
     * @return array An array containing information about each supported TIN type, including code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'TIN',
                'name' => 'Cypriot TIN',
                'description' => 'Cyprus Tax Identification Number',
            ],
        ];
    }

    /**
     * Validates a Cyprus TIN according to country-specific checksum rules.
     *
     * Applies a calculation involving the sum of even-position digits, a recoding of odd-position digits, and a modulo 26 operation to verify the check character.
     *
     * @param string $tin The Cyprus TIN to validate.
     * @return bool True if the TIN passes the Cyprus validation algorithm, false otherwise.
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
        $c9 = ord($tin[8]);

        $evenPositionNumbersSum = $c2 + $c4 + $c6 + $c8;

        $recodedSum = array_sum([
            $this->recodeValue($c1),
            $this->recodeValue($c3),
            $this->recodeValue($c5),
            $this->recodeValue($c7),
        ]);

        $remainderBy26 = ($evenPositionNumbersSum + $recodedSum) % 26;

        return $remainderBy26 + 65 === $c9;
    }

    private function recodeValue(int $x): int
    {
        switch ($x) {
            case 1:
                return 0;

            case 2:
                return 5;

            case 3:
                return 7;

            case 4:
                return 9;

            case 5:
                return 13;

            case 6:
                return 15;

            case 7:
                return 17;

            case 8:
                return 19;

            case 9:
                return 21;
        }

        return 1;
    }
}
