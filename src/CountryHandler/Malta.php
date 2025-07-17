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
     * @var string
     */
    public const PATTERN = '\d{7}[MGAPLHBZ]';

    /**
     * Returns a sample placeholder string for a Maltese TIN.
     *
     * @return string The placeholder value '12345678'.
     */
    public function getPlaceholder(): string
    {
        return '12345678';
    }

    /**
     * Returns an array describing the supported TIN types for Malta.
     *
     * @return array An array containing information about the Maltese TIN type, including its code, name, and description.
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

    /**
     * Determines if the provided TIN ends with a valid Maltese identifier letter.
     *
     * @param string $tin The tax identification number to validate.
     * @return bool True if the eighth character is one of 'M', 'G', 'A', 'P', 'L', 'H', 'B', or 'Z'; otherwise, false.
     */
    protected function hasValidRule(string $tin): bool
    {
        $valid = ['M', 'G', 'A', 'P', 'L', 'H', 'B', 'Z'];

        return in_array($tin[7], $valid, true);
    }
}
