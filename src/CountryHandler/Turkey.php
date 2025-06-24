<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * Turkey TIN validation.
 * Supports T.C. Kimlik No (11 digits personal) and Vergi Kimlik No (10 digits business).
 */
final class Turkey extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'TR';

    /**
     * @var int
     */
    public const LENGTH = 11; // Maximum length

    /**
     * @var string
     */
    public const MASK = '99999999999'; // Default to personal

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^\d{10,11}$';

    /**
     * Get placeholder text.
     */
    public function getPlaceholder(): string
    {
        return '12345678901';
    }

    /**
     * Get all TIN types supported by Turkey.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'TCKN',
                'name' => 'T.C. Kimlik No',
                'description' => 'Turkish national identification number',
            ],
            2 => [
                'code' => 'VKN',
                'name' => 'Vergi Kimlik No',
                'description' => 'Turkish tax identification number for businesses',
            ],
        ];
    }

    /**
     * Identify the TIN type for a given Turkish TIN.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);

        if (strlen($normalizedTin) === 11 && $this->isValidPersonalID($normalizedTin)) {
            return $this->getTinTypes()[1]; // T.C. Kimlik No
        }

        if (strlen($normalizedTin) === 10 && $this->isValidBusinessID($normalizedTin)) {
            return $this->getTinTypes()[2]; // Vergi Kimlik No
        }

        return null;
    }

    protected function hasValidLength(string $tin): bool
    {
        $length = strlen($tin);

        return 10 === $length || 11 === $length;
    }

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    protected function hasValidRule(string $tin): bool
    {
        $length = strlen($tin);

        if (11 === $length) {
            return $this->isValidPersonalID($tin);
        }

        if (10 === $length) {
            return $this->isValidBusinessID($tin);
        }

        return false;
    }

    /**
     * Validate Vergi Kimlik No (business tax ID).
     */
    private function isValidBusinessID(string $id): bool
    {
        // Cannot be all zeros
        if ('0000000000' === $id) {
            return false;
        }

        // Apply checksum algorithm
        $v = [];

        for ($i = 0; 9 > $i; ++$i) {
            $v[$i + 1] = (int) $id[$i];
        }

        $lastDigit = (int) $id[9];

        for ($i = 1; 9 >= $i; ++$i) {
            $v[$i] = ($v[$i] + $i) % 10;
        }

        for ($i = 1; 9 >= $i; ++$i) {
            $v[$i] = ($v[$i] * 2 ** $i) % 9;

            if (0 === $v[$i]) {
                $v[$i] = 9;
            }
        }

        $sum = 0;

        for ($i = 1; 9 >= $i; ++$i) {
            $sum += $v[$i];
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $checkDigit === $lastDigit;
    }

    /**
     * Validate T.C. Kimlik No (personal ID).
     * Uses specific checksum algorithm.
     */
    private function isValidPersonalID(string $id): bool
    {
        // First digit cannot be 0
        if ('0' === $id[0]) {
            return false;
        }

        // Cannot be all same digits
        if (preg_match('/^(\d)\1{10}$/', $id)) {
            return false;
        }

        // Calculate first check digit (10th digit)
        $oddSum = 0;
        $evenSum = 0;

        for ($i = 0; 9 > $i; ++$i) {
            if ($i % 2 === 0) {
                $oddSum += (int) $id[$i];
            } else {
                $evenSum += (int) $id[$i];
            }
        }

        $checkDigit1 = ((7 * $oddSum) - $evenSum) % 10;

        if (0 > $checkDigit1) {
            $checkDigit1 += 10;
        }

        if ((int) $id[9] !== $checkDigit1) {
            return false;
        }

        // Calculate second check digit (11th digit)
        $totalSum = 0;

        for ($i = 0; 10 > $i; ++$i) {
            $totalSum += (int) $id[$i];
        }

        $checkDigit2 = $totalSum % 10;

        return (int) $id[10] === $checkDigit2;
    }
}
