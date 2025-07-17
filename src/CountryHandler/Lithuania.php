<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Lithuania.
 */
final class Lithuania extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'LT';

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
    public const PATTERN = '[1-6]\d{2}[0-1]\d[0-3]\d{5}';

    /**
     * Returns a sample valid Lithuanian TIN for use as a placeholder.
     *
     * @return string Example Lithuanian TIN.
     */
    public function getPlaceholder(): string
    {
        return '33309240064';
    }

    /**
     * Returns an array of supported Lithuanian TIN types.
     *
     * @return array An array containing information about each supported TIN type, including code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'AK',
                'name' => 'Lithuanian AK',
                'description' => 'Lithuanian Personal Code (Asmens kodas)',
            ],
        ];
    }

    /**
     * Validates a Lithuanian TIN using the official checksum algorithm.
     *
     * Applies a two-step weighted checksum calculation to verify the control digit of the provided TIN.
     *
     * @param string $tin The Lithuanian TIN to validate.
     * @return bool True if the TIN passes the checksum validation; otherwise, false.
     */
    protected function hasValidRule(string $tin): bool
    {
        $sum = 0;
        $c11 = (int) (substr($tin, 10));

        for ($i = 0; 10 > $i; ++$i) {
            $sum += $this->multiplyAccordingToWeight((int) (substr($tin, $i, 1)), $i);
        }
        $remainderBy11 = $sum % 11;

        if (10 !== $remainderBy11) {
            return $c11 === $remainderBy11;
        }
        $sum2 = 0;

        for ($j = 0; 10 > $j; ++$j) {
            $sum2 += $this->multiplyAccordingToWeight2((int) (substr($tin, $j, 1)), $j);
        }
        $remainderBy11 = $sum2 % 11;

        if (10 === $remainderBy11) {
            return 0 === $c11;
        }

        return $c11 === $remainderBy11;
    }

    private function multiplyAccordingToWeight(int $val, int $index): int
    {
        switch ($index) {
            case 9:
            case 0:
                return $val * 1;

            case 1:
                return $val * 2;

            case 2:
                return $val * 3;

            case 3:
                return $val * 4;

            case 4:
                return $val * 5;

            case 5:
                return $val * 6;

            case 6:
                return $val * 7;

            case 7:
                return $val * 8;

            case 8:
                return $val * 9;

            default:
                return -1;
        }
    }

    /**
     * Multiplies a digit by its position-specific weight for the second Lithuanian TIN checksum calculation.
     *
     * Returns -1 if the index is not in the range 0–9.
     *
     * @param int $val The digit to be weighted.
     * @param int $index The position index of the digit (0-based).
     * @return int The weighted value or -1 for invalid indices.
     */
    private function multiplyAccordingToWeight2(int $val, int $index): int
    {
        switch ($index) {
            case 9:
            case 0:
                return $val * 3;

            case 1:
                return $val * 4;

            case 2:
                return $val * 5;

            case 3:
                return $val * 6;

            case 4:
                return $val * 7;

            case 5:
                return $val * 8;

            case 6:
                return $val * 9;

            case 7:
                return $val * 1;

            case 8:
                return $val * 2;

            default:
                return -1;
        }
    }
}
