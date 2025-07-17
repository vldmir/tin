<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * Slovakia.
 */
final class Slovakia extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'SK';

    /**
     * @var int
     */
    public const LENGTH = 10;

    /**
     * @var string
     */
    public const MASK = '9999999999';

    public const PATTERN = '([1-9]\d[234789]\d{7})|(\d{2}[0156]\d[0-3]\d{4,5})';

    /**
     * Returns a sample Slovak TIN as a placeholder value.
     *
     * @return string Example TIN string for Slovakia.
     */
    public function getPlaceholder(): string
    {
        return '7103192745';
    }

    /**
     * Returns an array describing the supported Slovak TIN types.
     *
     * @return array An array of TIN type definitions, each including a code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'RC',
                'name' => 'Slovak RC',
                'description' => 'Slovak Birth Number (Rodné číslo)',
            ],
        ];
    }

    /**
     * Validates a Slovak TIN according to country-specific checksum rules.
     *
     * For 10-digit TINs, checks if the number is divisible by 11 or if the last digit matches the calculated checksum. For other lengths, returns true by default.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN passes the Slovak validation rules, false otherwise.
     */
    public function hasValidRule(string $tin): bool
    {
        if (strlen($tin) === 10) {
            if ((int) $tin % 11 === 0) {
                return true;
            }

            return (int) substr($tin, 9, 1) === ((int) substr($tin, 0, 9) % 11) % 10;
        }

        return true;
    }

    /**
     * Determines if the provided TIN has a valid length according to Slovak rules.
     *
     * For TINs where the first two digits are less than 54, both 9- and 10-digit lengths are accepted. Otherwise, only the standard length is valid.
     *
     * @param string $tin The Tax Identification Number to check.
     * @return bool True if the TIN length is valid for Slovakia, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        $c1c2 = substr($tin, 0, 2);
        $hasValidLength = parent::hasValidLength($tin);

        if (54 > $c1c2) {
            return $hasValidLength || $this->matchLength($tin, self::LENGTH - 1);
        }

        return $hasValidLength;
    }
}
