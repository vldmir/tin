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

    public function getPlaceholder(): string
    {
        return '123456789';
    }

    /**
     * Get all TIN types supported by Portugal.
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
