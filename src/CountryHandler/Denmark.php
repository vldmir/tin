<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function in_array;

/**
 * Denmark.
 */
final class Denmark extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'DK';

    /**
     * @var int
     */
    public const LENGTH = 10;

    /**
     * @var string
     */
    public const MASK = '999999-9999';

    /**
     * @var string
     */
    public const PATTERN = '[0-3]\d[0-1]\d{3}\d{4}';

    /**
     * Returns a sample valid Danish CPR number without formatting.
     *
     * @return string Example TIN: '2110625629'.
     */
    public function getPlaceholder(): string
    {
        return '2110625629';
    }

    /**
     * Returns an array describing the supported TIN types for Denmark.
     *
     * @return array An array of TIN type definitions, including code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'CPR',
                'name' => 'Danish CPR',
                'description' => 'Danish Central Person Register Number (CPR-nummer)',
            ],
        ];
    }

    /**
     * Checks if the date encoded in the TIN is a valid calendar date in either the 1900s or 2000s.
     *
     * @param string $tin The Danish TIN (CPR number) to validate.
     * @return bool True if the extracted date is valid for either century, false otherwise.
     */
    protected function hasValidDate(string $tin): bool
    {
        $day = (int) (substr($tin, 0, 2));
        $month = (int) (substr($tin, 2, 2));
        $year = (int) (substr($tin, 4, 2));

        $d1 = checkdate($month, $day, 1900 + $year);
        $d2 = checkdate($month, $day, 2000 + $year);

        return $d1 || $d2;
    }

    /**
     * Validates a Danish CPR number according to official rules, including exceptions for certain birth dates and modulus 11 checksum.
     *
     * Returns true if the TIN passes all structural and checksum rules, accounting for special cases where modulus 11 validation is not required for specific birth dates. Returns false if the TIN fails any mandatory rule or checksum.
     *
     * @param string $tin The Danish CPR number to validate.
     * @return bool True if the CPR number is valid according to Danish rules; false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $serialNumber = (int) (substr($tin, 6, 4));
        $dayOfBirth = (int) (substr($tin, 0, 2));
        $monthOfBirth = (int) (substr($tin, 2, 2));
        $yearOfBirth = (int) (substr($tin, 4, 2));

        if (37 <= $yearOfBirth && 57 >= $yearOfBirth && 5000 <= $serialNumber && 8999 >= $serialNumber) {
            return false;
        }

        $excludedYears = [60, 64, 65, 66, 69, 70, 74, 80, 82, 84, 85, 86, 87, 88, 89, 90, 91, 92];

        if (1 === $dayOfBirth && 1 === $monthOfBirth && in_array($yearOfBirth, $excludedYears, true)) {
            return true;
        }

        $c1 = $this->digitAt($tin, 0);
        $c2 = $this->digitAt($tin, 1);
        $c3 = $this->digitAt($tin, 2);
        $c4 = $this->digitAt($tin, 3);
        $c5 = $this->digitAt($tin, 4);
        $c6 = $this->digitAt($tin, 5);
        $c7 = $this->digitAt($tin, 6);
        $c8 = $this->digitAt($tin, 7);
        $c9 = $this->digitAt($tin, 8);
        $c10 = $this->digitAt($tin, 9);
        $sum = $c1 * 4 + $c2 * 3 + $c3 * 2 + $c4 * 7 + $c5 * 6 + $c6 * 5 + $c7 * 4 + $c8 * 3 + $c9 * 2;
        $remainderBy11 = $sum % 11;

        if (1 === $remainderBy11) {
            return false;
        }

        if (0 === $remainderBy11) {
            return 0 === $c10;
        }

        return 11 - $remainderBy11 === $c10;
    }
}
