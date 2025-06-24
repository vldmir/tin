<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Finland.
 */
final class Finland extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'FI';

    /**
     * @var int
     */
    public const LENGTH = 11;

    /**
     * @var string
     */
    public const MASK = '999999-999A';

    /**
     * @var string
     */
    public const PATTERN = '[0-3]\d[0-1]\d{3}[+-A]\d{3}[0-9A-Z]';

    /**
     * Returns a sample Finnish TIN (Personal Identity Code) as a placeholder.
     *
     * @return string Example TIN in the correct Finnish format.
     */
    public function getPlaceholder(): string
    {
        return '131052-308T';
    }

    /**
     * Returns an array describing the Finnish TIN types supported.
     *
     * @return array An array containing information about the Finnish HETU (Personal Identity Code) type.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'HETU',
                'name' => 'Finnish HETU',
                'description' => 'Finnish Personal Identity Code (Henkilötunnus)',
            ],
        ];
    }

    /**
     * Checks if the date portion of a Finnish TIN is valid based on its encoded century and date fields.
     *
     * @param string $tin The Finnish TIN to validate.
     * @return bool True if the TIN contains a valid date, otherwise false.
     */
    protected function hasValidDate(string $tin): bool
    {
        $day = (int) (substr($tin, 0, 2));
        $month = (int) (substr($tin, 2, 2));
        $year = (int) (substr($tin, 4, 2));
        $c7 = substr($tin, 6, 1);

        if ('+' === $c7) {
            return checkdate($month, $day, 1800 + $year);
        }

        if ('-' === $c7) {
            return checkdate($month, $day, 1900 + $year);
        }

        return 'A' === $c7 && checkdate($month, $day, 2000 + $year);
    }

    protected function hasValidRule(string $tin): bool
    {
        $number = (int) (substr($tin, 0, 6) . substr($tin, 7, 3));
        $remainderBy31 = $number % 31;
        $c11 = $tin[10];

        return $this->getMatch($remainderBy31) === $c11;
    }

    /**
     * Returns the character corresponding to a given number for Finnish TIN checksum calculation.
     *
     * For numbers 0–9, returns the number as a string. For numbers 10–30, returns a specific letter according to the Finnish TIN checksum mapping. Returns a space character for numbers outside the 0–30 range.
     *
     * @param int $number The number to map to a checksum character.
     * @return string The corresponding checksum character or a space if out of range.
     */
    private function getMatch(int $number): string
    {
        if (10 > $number) {
            return (string) $number;
        }

        switch ($number) {
            case 10:
                return 'A';

            case 11:
                return 'B';

            case 12:
                return 'C';

            case 13:
                return 'D';

            case 14:
                return 'E';

            case 15:
                return 'F';

            case 16:
                return 'H';

            case 17:
                return 'J';

            case 18:
                return 'K';

            case 19:
                return 'L';

            case 20:
                return 'M';

            case 21:
                return 'N';

            case 22:
                return 'P';

            case 23:
                return 'R';

            case 24:
                return 'S';

            case 25:
                return 'T';

            case 26:
                return 'U';

            case 27:
                return 'V';

            case 28:
                return 'W';

            case 29:
                return 'X';

            case 30:
                return 'Y';

            default:
                return ' ';
        }
    }
}
