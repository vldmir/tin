<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function in_array;
use function strlen;

/**
 * China TIN validation.
 * Supports Personal ID (18 digits or 17+X) and Unified Social Credit Code (18 alphanumeric).
 */
final class China extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'CN';

    /**
     * @var int
     */
    public const LENGTH = 18;

    /**
     * @var string
     */
    public const MASK = '999999999999999999'; // Default to personal

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^(\d{17}[\dX]|[0-9A-HJ-NPQRTUWXY]{2}\d{6}[0-9A-HJ-NPQRTUWXY]{10})$';

    /**
     * Business Pattern: 18 alphanumeric characters.
     *
     * @var string
     */
    public const PATTERN_BUSINESS = '^[0-9A-HJ-NPQRTUWXY]{2}\d{6}[0-9A-HJ-NPQRTUWXY]{10}$';

    /**
     * Personal ID Pattern: 18 digits or 17 digits + X.
     *
     * @var string
     */
    public const PATTERN_PERSONAL = '^\d{17}[\dX]$';

    /**
     * Returns the input mask string for Chinese TIN entry, defaulting to the personal ID format (18 digits).
     *
     * @return string The input mask for TIN entry.
     */
    public function getInputMask(): string
    {
        // Default to personal ID format
        return '999999999999999999';
    }

    /**
     * Returns a sample placeholder string for Chinese TIN input.
     *
     * @return string Example TIN placeholder, e.g., '11010519491231002X'.
     */
    public function getPlaceholder(): string
    {
        return '11010519491231002X';
    }

    /**
     * Returns an array of supported Chinese TIN types, including personal ID numbers and business entity codes.
     *
     * @return array An array containing information about each supported TIN type, including code, name, and description.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'ID',
                'name' => 'Citizen ID Number',
                'description' => 'Chinese citizen identification number',
            ],
            2 => [
                'code' => 'USCC',
                'name' => 'Unified Social Credit Code',
                'description' => 'Business entity identification code',
            ],
        ];
    }

    /**
     * Determines the type of a Chinese TIN (Personal ID or Business Code) based on its format and validity.
     *
     * @param string $tin The input Tax Identification Number to be analyzed.
     * @return array|null The TIN type information array if valid, or null if the TIN is invalid or unrecognized.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);

        if ($this->matchPattern($normalizedTin, self::PATTERN_PERSONAL) && $this->isValidPersonalID($normalizedTin)) {
            return $this->getTinTypes()[1]; // Personal ID
        }

        if ($this->matchPattern($normalizedTin, self::PATTERN_BUSINESS) && $this->isValidBusinessCode($normalizedTin)) {
            return $this->getTinTypes()[2]; // Business Code
        }

        return null;
    }

    /**
     * Checks if the TIN has the required length of 18 characters.
     *
     * @param string $tin The Tax Identification Number to check.
     * @return bool True if the TIN is exactly 18 characters long, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        return strlen($tin) === self::LENGTH;
    }

    /**
     * Checks if the provided TIN matches the combined pattern for valid Chinese personal or business identifiers.
     *
     * @param string $tin The Tax Identification Number to validate.
     * @return bool True if the TIN matches the required pattern, false otherwise.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates the TIN according to its type-specific rules.
     *
     * Determines whether the provided TIN is a valid personal ID or business code by matching its pattern and applying the corresponding validation logic.
     *
     * @param string $tin The normalized TIN to validate.
     * @return bool True if the TIN passes all type-specific validation rules, false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        // Check if it's personal ID
        if ($this->matchPattern($tin, self::PATTERN_PERSONAL)) {
            return $this->isValidPersonalID($tin);
        }

        // Check if it's business code
        if ($this->matchPattern($tin, self::PATTERN_BUSINESS)) {
            return $this->isValidBusinessCode($tin);
        }

        return false;
    }

    /**
     * Normalizes the TIN by removing all non-alphanumeric characters except 'X' and converting to uppercase.
     *
     * Preserves the 'X' character, which is valid in Chinese personal ID numbers.
     *
     * @param string $tin The input Tax Identification Number.
     * @return string The normalized TIN string.
     */
    protected function normalizeTin(string $tin): string
    {
        // Remove non-alphanumeric characters but preserve X
        if (null !== $string = preg_replace('#[^[:alnum:]X]#u', '', $tin)) {
            return strtoupper($string);
        }

        return '';
    }

    /**
     * Checks if the provided year, month, and day constitute a valid birth date.
     *
     * Validates that the year is within a reasonable range (not before 1900 and not in the future), the month is between 1 and 12, and the day is between 1 and 31. Uses PHP's `checkdate` to confirm the date is valid in the calendar.
     *
     * @param int $year The year component of the date.
     * @param int $month The month component of the date.
     * @param int $day The day component of the date.
     * @return bool True if the date is a valid birth date, false otherwise.
     */
    private function isValidBirthDate(int $year, int $month, int $day): bool
    {
        // Basic range validation
        if (1900 > $year || (int) date('Y') < $year) {
            return false;
        }

        if (1 > $month || 12 < $month) {
            return false;
        }

        if (1 > $day || 31 < $day) {
            return false;
        }

        // Check if date is valid
        return checkdate($month, $day, $year);
    }

    /**
     * Validates a Chinese Unified Social Credit Code (business code).
     *
     * Checks that the code contains only allowed characters and that its checksum is correct.
     *
     * @param string $code The business code to validate.
     * @return bool True if the code is valid; otherwise, false.
     */
    private function isValidBusinessCode(string $code): bool
    {
        // Valid characters for business code (excludes I, O, S, V, Z)
        $validChars = '0123456789ABCDEFGHJKLMNPQRTUWXY';

        // Check if all characters are valid
        for ($i = 0; strlen($code) > $i; ++$i) {
            if (strpos($validChars, $code[$i]) === false) {
                return false;
            }
        }

        // Validate checksum
        return $this->isValidBusinessCodeChecksum($code);
    }

    /**
     * Validates the checksum of a Chinese business code (Unified Social Credit Code).
     *
     * Calculates the checksum using the prescribed character mapping and weighting algorithm, and verifies that the final character matches the expected checksum character.
     *
     * @param string $code The 18-character business code to validate.
     * @return bool True if the checksum is valid, false otherwise.
     */
    private function isValidBusinessCodeChecksum(string $code): bool
    {
        $charMap = [
            '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4,
            '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
            'A' => 10, 'B' => 11, 'C' => 12, 'D' => 13, 'E' => 14,
            'F' => 15, 'G' => 16, 'H' => 17, 'J' => 18, 'K' => 19,
            'L' => 20, 'M' => 21, 'N' => 22, 'P' => 23, 'Q' => 24,
            'R' => 25, 'T' => 26, 'U' => 27, 'W' => 28, 'X' => 29, 'Y' => 30,
        ];

        $weights = [1, 3, 9, 27, 19, 26, 16, 17, 20, 29, 25, 13, 8, 24, 10, 30, 28];

        $sum = 0;

        for ($i = 0; 17 > $i; ++$i) {
            $sum += $charMap[$code[$i]] * $weights[$i];
        }

        $checksum = 31 - ($sum % 31);

        if (31 === $checksum) {
            $checksum = 0;
        }

        // Find the character for the checksum
        $checksumChar = array_search($checksum, $charMap, true);

        return $code[17] === $checksumChar;
    }

    /**
     * Validates a Chinese Personal ID number by checking the region code, birth date, and checksum.
     *
     * @param string $id The 18-character Personal ID number to validate.
     * @return bool True if the Personal ID is valid; otherwise, false.
     */
    private function isValidPersonalID(string $id): bool
    {
        // Extract components
        $region = substr($id, 0, 6);
        $birthDate = substr($id, 6, 8);
        $sequence = substr($id, 14, 3);
        $checksum = substr($id, 17, 1);

        // Validate region code (basic check)
        if (!$this->isValidRegionCode($region)) {
            return false;
        }

        // Validate birth date
        $year = (int) substr($birthDate, 0, 4);
        $month = (int) substr($birthDate, 4, 2);
        $day = (int) substr($birthDate, 6, 2);

        if (!$this->isValidBirthDate($year, $month, $day)) {
            return false;
        }

        // Validate checksum
        return $this->isValidPersonalIDChecksum($id);
    }

    /**
     * Checks if the checksum digit of a Chinese personal ID (resident identity card number) is valid.
     *
     * Calculates the checksum using the official weighting algorithm and compares it to the last character of the ID.
     *
     * @param string $id The 18-character personal ID to validate.
     * @return bool True if the checksum is valid, false otherwise.
     */
    private function isValidPersonalIDChecksum(string $id): bool
    {
        $weights = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $checksumMap = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];

        $sum = 0;

        for ($i = 0; 17 > $i; ++$i) {
            $sum += ((int) $id[$i]) * $weights[$i];
        }

        $checksumIndex = $sum % 11;
        $expectedChecksum = $checksumMap[$checksumIndex];

        return $id[17] === $expectedChecksum;
    }

    /**
     * Checks if the provided region code corresponds to a valid Chinese province code.
     *
     * @param string $region The region code extracted from a Chinese personal ID.
     * @return bool True if the region code is valid, false otherwise.
     */
    private function isValidRegionCode(string $region): bool
    {
        // First two digits represent province
        $province = (int) substr($region, 0, 2);

        // Valid province codes (11-65, excluding some)
        $validProvinces = [
            11, 12, 13, 14, 15, // Beijing, Tianjin, Hebei, Shanxi, Inner Mongolia
            21, 22, 23,         // Liaoning, Jilin, Heilongjiang
            31, 32, 33, 34, 35, 36, 37, // Shanghai, Jiangsu, Zhejiang, Anhui, Fujian, Jiangxi, Shandong
            41, 42, 43, 44, 45, 46,     // Henan, Hubei, Hunan, Guangdong, Guangxi, Hainan
            50, 51, 52, 53, 54,         // Chongqing, Sichuan, Guizhou, Yunnan, Tibet
            61, 62, 63, 64, 65,          // Shaanxi, Gansu, Qinghai, Ningxia, Xinjiang
        ];

        return in_array($province, $validProvinces, true);
    }
}
