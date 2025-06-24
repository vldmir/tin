<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

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
    public const MASK = '9 99 99 99 999 999';

    /**
     * @var string
     */
    public const PATTERN = '[0-3]\d{12}';

    /**
     * Returns a sample formatted French TIN as a placeholder string.
     *
     * @return string Example of a French Tax Identification Number in the standard format.
     */
    public function getPlaceholder(): string
    {
        return '1 23 45 67 890 123';
    }

    /**
     * Returns an array describing the supported French TIN types.
     *
     * @return array An array containing information about the French Tax Identification Number (Numéro de Sécurité Sociale).
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'TIN',
                'name' => 'French TIN',
                'description' => 'French Tax Identification Number (Numéro de Sécurité Sociale)',
            ],
        ];
    }

    /**
     * Validates a French TIN using the official checksum rule.
     *
     * The method calculates a remainder from the first 10 digits of the TIN divided by 511,
     * then compares it to the check digits extracted from the TIN according to the remainder's value.
     *
     * @param string $tin The French Tax Identification Number to validate.
     * @return bool True if the TIN passes the checksum validation; otherwise, false.
     */
    protected function hasValidRule(string $tin): bool
    {
        $number = (int) (substr($tin, 0, 10));

        $remainderBy511 = $number % 511;

        $checkDigits = 100 > $remainderBy511
            ? 10 > $remainderBy511 ? (int) (substr($tin, 12, 13)) : (int) (substr($tin, 11, 13))
            : (int) (substr($tin, 10, 13));

        return $remainderBy511 === $checkDigits;
    }
}
