<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function in_array;
use function strlen;

/**
 * Argentina TIN validation.
 * Supports CUIT (Clave Única de Identificación Tributaria).
 */
final class Argentina extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'AR';

    /**
     * @var int
     */
    public const LENGTH = 11;

    /**
     * @var string
     */
    public const MASK = '99-99999999-9';

    /**
     * CUIT Pattern: 99-99999999-9.
     *
     * @var string
     */
    public const PATTERN = '^\d{2}-?\d{8}-?\d{1}$';

    /**
     * Format input according to CUIT format.
     */
    public function formatInput(string $input): string
    {
        $normalized = preg_replace('/[^0-9]/', '', $input);

        if ('' === $normalized) {
            return '';
        }

        // Format as: 99-99999999-9
        if (strlen($normalized) >= 2) {
            $result = substr($normalized, 0, 2);

            if (strlen($normalized) > 2) {
                $result .= '-' . substr($normalized, 2, 8);

                if (strlen($normalized) > 10) {
                    $result .= '-' . substr($normalized, 10, 1);
                }
            }

            return $result;
        }

        return $normalized;
    }

    /**
     * Get placeholder text.
     */
    public function getPlaceholder(): string
    {
        return '20-12345678-9';
    }

    /**
     * Get all TIN types supported by Argentina.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'CUIT',
                'name' => 'Clave Única de Identificación Tributaria',
                'description' => 'Unique Tax Identification Key for individuals and companies',
            ],
        ];
    }

    protected function hasValidLength(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

        return strlen($normalizedTin) === self::LENGTH;
    }

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    protected function hasValidRule(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

        if (strlen($normalizedTin) !== 11) {
            return false;
        }

        // Extract parts
        $type = substr($normalizedTin, 0, 2);
        $number = substr($normalizedTin, 2, 8);
        $checkDigit = (int) substr($normalizedTin, 10, 1);

        // Validate type
        if (!$this->isValidType($type)) {
            return false;
        }

        // Validate checksum
        return $this->validateChecksum($normalizedTin);
    }

    /**
     * Validate CUIT type prefix.
     */
    private function isValidType(string $type): bool
    {
        // Valid prefixes:
        // 20, 23, 24, 27: Individuals
        // 30, 33, 34: Companies
        $validTypes = ['20', '23', '24', '27', '30', '33', '34'];

        return in_array($type, $validTypes, true);
    }

    /**
     * Validate CUIT checksum using modulo 11.
     */
    private function validateChecksum(string $cuit): bool
    {
        $weights = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        for ($i = 0; 10 > $i; ++$i) {
            $sum += ((int) $cuit[$i]) * $weights[$i];
        }

        $remainder = $sum % 11;
        $checkDigit = 11 - $remainder;

        // Special cases
        if (11 === $checkDigit) {
            $checkDigit = 0;
        } elseif (10 === $checkDigit) {
            // For type 20 (male), it should be 23 with check digit 9
            // For type 27 (female), it should be 23 with check digit 4
            // This is a simplification - in practice it's more complex
            return false;
        }

        return (int) $cuit[10] === $checkDigit;
    }
}
