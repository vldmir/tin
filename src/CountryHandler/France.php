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

    public function getPlaceholder(): string
    {
        return '1 23 45 67 890 066';
    }

    /**
     * Get all TIN types supported by France.
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
