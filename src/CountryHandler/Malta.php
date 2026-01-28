<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function in_array;

/**
 * Malta.
 */
final class Malta extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'MT';

    /**
     * @var int
     */
    public const LENGTH = 8;

    /**
     * @var string
     */
    public const MASK = '9999999A';

    /**
     * Pattern: 7 digits followed by any uppercase letter.
     * Valid letters (M, G, A, P, L, H, B, Z) are checked in hasValidRule.
     *
     * @var string
     */
    public const PATTERN = '^\d{7}[A-Z]$';

    public function getPlaceholder(): string
    {
        return '12345678';
    }

    /**
     * Get all TIN types supported by Malta.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'TIN',
                'name' => 'Maltese TIN',
                'description' => 'Malta Tax Identification Number',
            ],
        ];
    }

    protected function hasValidRule(string $tin): bool
    {
        $valid = ['M', 'G', 'A', 'P', 'L', 'H', 'B', 'Z'];

        return in_array($tin[7], $valid, true);
    }
}
