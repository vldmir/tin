<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

/**
 * Greece.
 */
final class Greece extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'GR';

    /**
     * @var int
     */
    public const LENGTH = 9;

    /**
     * @var string
     */
    public const PATTERN = '\d{9}';

    /**
     * @var string
     */
    public const MASK = '999999999';

    public function getPlaceholder(): string
    {
        return '123456789';
    }

    /**
     * Get all TIN types supported by Greece.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'AFM',
                'name' => 'Greek AFM',
                'description' => 'Greek Tax Registration Number (Arithmos Forologikou Mitroou)',
            ],
        ];
    }
}
