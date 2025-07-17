<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Belgium.
 */
final class Belgium extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'BE';

    /**
     * @var int
     */
    public const LENGTH = 11;

    /**
     * @var string
     */
    public const MASK = '99.99.99-999.99';

    /**
     * @var string
     */
    public const PATTERN = '\d{2}[0-1]\d[0-3]\d{6}';

    /**
     * Returns a sample formatted Belgian TIN for use as a placeholder.
     *
     * @return string Example Belgian TIN in the correct format.
     */
    public function getPlaceholder(): string
    {
        return '85.07.30-033.61';
    }

    /**
     * Returns an array describing the supported Tax Identification Number (TIN) types for Belgium.
     *
     * @return array An array containing information about each supported TIN type, including code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'TIN',
                'name' => 'Belgian TIN',
                'description' => 'Belgian Tax Identification Number (Numéro de Registre National)',
            ],
        ];
    }

    /**
     * Determines if the provided TIN contains a valid date component.
     *
     * @param string $tin The Belgian TIN to check.
     * @return bool True if the TIN encodes a valid date; false otherwise.
     */
    protected function hasValidDate(string $tin): bool
    {
        return 0 !== $this->getDateType($tin);
    }

    protected function hasValidRule(string $tin): bool
    {
        return $this->isFollowBelgiumRule1AndIsDateValid($tin) || $this->isFollowBelgiumRule2AndIsDateValid($tin);
    }

    private function getDateType(string $tin): int
    {
        $year = (int) (substr($tin, 0, 2));
        $month = (int) (substr($tin, 2, 2));
        $day = (int) (substr($tin, 4, 2));

        $y1 = checkdate($month, $day, 1900 + $year);
        $y2 = checkdate($month, $day, 2000 + $year);

        if (0 === $day || 0 === $month || ($y1 && $y2)) {
            return 3;
        }

        if ($y1) {
            return 1;
        }

        if ($y2) {
            return 2;
        }

        return 0;
    }

    private function isFollowBelgiumRule1(string $tin): bool
    {
        $divisionRemainderBy97 = (int) (substr($tin, 0, 9)) % 97;

        return 97 - $divisionRemainderBy97 === (int) (substr($tin, 9, 3));
    }

    private function isFollowBelgiumRule1AndIsDateValid(string $tin): bool
    {
        $dateType = $this->getDateType($tin);

        return $this->isFollowBelgiumRule1($tin) && (1 === $dateType || 3 === $dateType);
    }

    private function isFollowBelgiumRule2(string $tin): bool
    {
        $divisionRemainderBy97 = (int) ('2' . substr($tin, 0, 9)) % 97;

        return 97 - $divisionRemainderBy97 === (int) (substr($tin, 9, 3));
    }

    /**
     * Checks if the TIN satisfies Belgian validation rule 2 and contains a valid date for the 2000s or both centuries.
     *
     * @param string $tin The Belgian TIN to validate.
     * @return bool True if the TIN passes rule 2 and the date is valid for the 2000s or both centuries; otherwise, false.
     */
    private function isFollowBelgiumRule2AndIsDateValid(string $tin): bool
    {
        $dateType = $this->getDateType($tin);

        return $this->isFollowBelgiumRule2($tin) && 2 <= $dateType;
    }
}
