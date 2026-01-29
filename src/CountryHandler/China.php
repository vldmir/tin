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
     * Get input mask based on the TIN type.
     */
    public function getInputMask(): string
    {
        // Default to personal ID format
        return '999999999999999999';
    }

    /**
     * Get placeholder based on the TIN type.
     */
    public function getPlaceholder(): string
    {
        return '11010519491231002X';
    }

    /**
     * Get all TIN types supported by China.
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
     * Identify the TIN type for a given Chinese TIN.
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

    protected function hasValidLength(string $tin): bool
    {
        return strlen($tin) === self::LENGTH;
    }

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

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
     * Override normalizeTin to preserve X in personal IDs.
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
     * Validate birth date.
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
     * Validate Business Code (Unified Social Credit Code).
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
     * Validate business code checksum.
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
     * Validate Personal ID number.
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
     * Validate personal ID checksum.
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
     * Validate region code.
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
