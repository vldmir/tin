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

    public function getPlaceholder(): string
    {
        return '7103192745';
    }

    /**
     * Get all TIN types supported by Slovakia.
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
