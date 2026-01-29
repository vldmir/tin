<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

/**
 * Finland.
 *
 * Finnish Personal Identity Code (Henkilötunnus/HETU) format: DDMMYY-NNNC
 * Where the separator (+, -, A) indicates century but is removed during normalization.
 * Validation works with 10-character normalized format: DDMMYYNNNC
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
    public const LENGTH = 10;

    /**
     * @var string
     */
    public const MASK = '999999-999A';

    /**
     * @var string
     */
    public const PATTERN = '[0-3]\d[0-1]\d{6}[0-9A-Z]';

    public function getPlaceholder(): string
    {
        return '131052-308T';
    }

    /**
     * Get all TIN types supported by Finland.
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

    protected function hasValidDate(string $tin): bool
    {
        $day = (int) (substr($tin, 0, 2));
        $month = (int) (substr($tin, 2, 2));
        $year = (int) (substr($tin, 4, 2));

        // Century indicator is lost during normalization, so check if date is valid
        // in any possible century (1800s, 1900s, or 2000s)
        return checkdate($month, $day, 1800 + $year)
            || checkdate($month, $day, 1900 + $year)
            || checkdate($month, $day, 2000 + $year);
    }

    protected function hasValidRule(string $tin): bool
    {
        // Format after normalization: DDMMYYNNNC (10 chars)
        // Checksum is calculated from DDMMYYNNN (9 digits)
        $number = (int) (substr($tin, 0, 9));
        $remainderBy31 = $number % 31;
        $c10 = $tin[9];

        return $this->getMatch($remainderBy31) === $c10;
    }

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
