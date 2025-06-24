<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

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
    public const MASK = '999999999';

    /**
     * @var string
     */
    public const PATTERN = '\d{9}';

    /**
     * Returns a sample Greek TIN (Tax Identification Number) placeholder.
     *
     * @return string The placeholder TIN value for Greece.
     */
    public function getPlaceholder(): string
    {
        return '123456789';
    }

    /**
     * Returns an array of supported Greek TIN types with their codes, names, and descriptions.
     *
     * @return array An associative array describing each supported TIN type for Greece.
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
