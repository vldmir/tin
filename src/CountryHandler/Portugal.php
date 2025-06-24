<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Portugal.
 */
final class Portugal extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'PT';

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
     * Returns a sample Portuguese Tax Identification Number (TIN) placeholder.
     *
     * @return string Example TIN in the correct format.
     */
    public function getPlaceholder(): string
    {
        return '123456789';
    }

    /**
     * Returns an array of supported Portuguese TIN types with their codes, names, and descriptions.
     *
     * @return array An associative array describing each supported TIN type for Portugal.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'NIF',
                'name' => 'Portuguese NIF',
                'description' => 'Portuguese Tax Identification Number (Número de Identificação Fiscal)',
            ],
        ];
    }

    /**
     * Validates a Portuguese TIN (NIF) using its official check digit algorithm.
     *
     * Applies the Portuguese NIF validation rule by calculating a weighted sum of the first eight digits,
     * determining the check digit, and comparing it to the ninth digit as specified by the official algorithm.
     *
     * @param string $tin The TIN to validate.
     * @return bool True if the TIN is valid according to the check digit rule, false otherwise.
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
        $sum = $c1 * 9 + $c2 * 8 + $c3 * 7 + $c4 * 6 + $c5 * 5 + $c6 * 4 + $c7 * 3 + $c8 * 2;
        $remainderBy11 = $sum % 11;
        $checkDigit = 11 - $remainderBy11;

        if (9 >= $checkDigit) {
            return $checkDigit === $c9;
        }

        if (10 === $checkDigit) {
            return 0 === $c9;
        }

        return 0 === $c9;
    }
}
