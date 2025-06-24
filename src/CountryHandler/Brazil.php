<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use function strlen;

/**
 * Brazil TIN validation.
 * Supports CPF (Cadastro de Pessoas Físicas) and CNPJ (Cadastro Nacional da Pessoa Jurídica).
 * Both formats include checksum validation.
 */
final class Brazil extends CountryHandler
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'BR';

    /**
     * @var int
     */
    public const LENGTH = 14; // Maximum length (CNPJ)

    /**
     * @var string
     */
    public const MASK = '999.999.999-99'; // Default to CPF

    /**
     * Combined pattern for all types.
     *
     * @var string
     */
    public const PATTERN = '^(\d{3}\.?\d{3}\.?\d{3}-?\d{2}|\d{2}\.?\d{3}\.?\d{3}\/?\d{4}-?\d{2})$';

    /**
     * CNPJ Pattern: 99.999.999/9999-99 (14 digits).
     *
     * @var string
     */
    public const PATTERN_CNPJ = '^\d{2}\.?\d{3}\.?\d{3}\/?\d{4}-?\d{2}$';

    /**
     * CPF Pattern: 999.999.999-99 (11 digits).
     *
     * @var string
     */
    public const PATTERN_CPF = '^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$';

    /**
     * Formats a Brazilian TIN input string as either CPF or CNPJ, applying standard punctuation.
     *
     * Removes all non-digit characters from the input. If the resulting number has 11 or fewer digits, formats it as CPF (`999.999.999-99`). If it has up to 14 digits, formats it as CNPJ (`99.999.999/9999-99`). Returns an empty string if the input contains no digits.
     *
     * @param string $input The raw TIN input string.
     * @return string The formatted CPF or CNPJ string, or an empty string if input is empty.
     */
    public function formatInput(string $input): string
    {
        $normalized = preg_replace('/[^0-9]/', '', $input);

        if ('' === $normalized) {
            return '';
        }

        // Determine format based on length
        if (strlen($normalized) <= 11) {
            // Format as CPF: 999.999.999-99
            $result = '';

            for ($i = 0; strlen($normalized) > $i && 11 > $i; ++$i) {
                if (3 === $i || 6 === $i) {
                    $result .= '.';
                } elseif (9 === $i) {
                    $result .= '-';
                }
                $result .= $normalized[$i];
            }

            return $result;
        }
        // Format as CNPJ: 99.999.999/9999-99
        $result = '';

        for ($i = 0; strlen($normalized) > $i && 14 > $i; ++$i) {
            if (2 === $i || 5 === $i) {
                $result .= '.';
            } elseif (8 === $i) {
                $result .= '/';
            } elseif (12 === $i) {
                $result .= '-';
            }
            $result .= $normalized[$i];
        }

        return $result;
    }

    /**
     * Returns the default input mask for Brazilian TINs in CPF format.
     *
     * @return string The input mask string '999.999.999-99'.
     */
    public function getInputMask(): string
    {
        // Default to CPF format
        return '999.999.999-99';
    }

    /**
     * Returns a placeholder string representing the standard CPF format.
     *
     * @return string The placeholder for CPF input (e.g., '123.456.789-09').
     */
    public function getPlaceholder(): string
    {
        return '123.456.789-09';
    }

    /**
     * Returns an array describing the supported Brazilian TIN types: CPF for individuals and CNPJ for businesses.
     *
     * @return array An array with metadata for CPF and CNPJ TIN types.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'CPF',
                'name' => 'Cadastro de Pessoas Físicas',
                'description' => 'Individual taxpayer registry identification',
            ],
            2 => [
                'code' => 'CNPJ',
                'name' => 'Cadastro Nacional da Pessoa Jurídica',
                'description' => 'Business taxpayer registry identification',
            ],
        ];
    }

    /**
     * Determines whether the given Brazilian TIN is a CPF or CNPJ and returns its type information.
     *
     * Normalizes the input and validates it as either a CPF (11 digits) or CNPJ (14 digits).
     * Returns the corresponding TIN type metadata if valid, or null if the input does not match either type.
     *
     * @param string $tin The Brazilian TIN to identify.
     * @return array|null The TIN type information if valid, or null if unrecognized or invalid.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $this->normalizeTin($tin));

        if (strlen($normalizedTin) === 11 && $this->isValidCPF($normalizedTin)) {
            return $this->getTinTypes()[1]; // CPF
        }

        if (strlen($normalizedTin) === 14 && $this->isValidCNPJ($normalizedTin)) {
            return $this->getTinTypes()[2]; // CNPJ
        }

        return null;
    }

    /**
     * Checks if the normalized TIN has a valid length for CPF (11 digits) or CNPJ (14 digits).
     *
     * @param string $tin The input Tax Identification Number.
     * @return bool True if the TIN has a valid length, false otherwise.
     */
    protected function hasValidLength(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);
        $length = strlen($normalizedTin);

        return 11 === $length || 14 === $length; // CPF or CNPJ
    }

    /**
     * Checks if the provided TIN matches the CPF or CNPJ format pattern.
     *
     * @param string $tin The tax identification number to validate.
     * @return bool True if the TIN matches the expected pattern, false otherwise.
     */
    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    /**
     * Validates the TIN by checking for repeated digits and verifying CPF or CNPJ checksums.
     *
     * @param string $tin The input Tax Identification Number.
     * @return bool True if the TIN passes rule-based validation for CPF or CNPJ; false otherwise.
     */
    protected function hasValidRule(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);

        // Check if all digits are the same (invalid)
        if (preg_match('/^(\d)\1+$/', $normalizedTin)) {
            return false;
        }

        // Validate based on length
        if (strlen($normalizedTin) === 11) {
            return $this->isValidCPF($normalizedTin);
        }

        if (strlen($normalizedTin) === 14) {
            return $this->isValidCNPJ($normalizedTin);
        }

        return false;
    }

    /**
     * Checks if a CNPJ number has valid check digits according to Brazilian rules.
     *
     * @param string $cnpj The numeric CNPJ string (14 digits, no formatting).
     * @return bool True if the CNPJ is valid, false otherwise.
     */
    private function isValidCNPJ(string $cnpj): bool
    {
        // Weights for first check digit
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        // Calculate first check digit
        $sum = 0;

        for ($i = 0; 12 > $i; ++$i) {
            $sum += ((int) $cnpj[$i]) * $weights1[$i];
        }

        $remainder = $sum % 11;
        $checkDigit1 = 2 > $remainder ? 0 : 11 - $remainder;

        if ((int) $cnpj[12] !== $checkDigit1) {
            return false;
        }

        // Weights for second check digit
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        // Calculate second check digit
        $sum = 0;

        for ($i = 0; 13 > $i; ++$i) {
            $sum += ((int) $cnpj[$i]) * $weights2[$i];
        }

        $remainder = $sum % 11;
        $checkDigit2 = 2 > $remainder ? 0 : 11 - $remainder;

        return (int) $cnpj[13] === $checkDigit2;
    }

    /**
     * Checks if a CPF number is valid by verifying its check digits according to official Brazilian rules.
     *
     * @param string $cpf The CPF number as a numeric string (11 digits, no formatting).
     * @return bool True if the CPF is valid; false otherwise.
     */
    private function isValidCPF(string $cpf): bool
    {
        // Calculate first check digit
        $sum = 0;

        for ($i = 0; 9 > $i; ++$i) {
            $sum += ((int) $cpf[$i]) * (10 - $i);
        }

        $remainder = $sum % 11;
        $checkDigit1 = 2 > $remainder ? 0 : 11 - $remainder;

        if ((int) $cpf[9] !== $checkDigit1) {
            return false;
        }

        // Calculate second check digit
        $sum = 0;

        for ($i = 0; 10 > $i; ++$i) {
            $sum += ((int) $cpf[$i]) * (11 - $i);
        }

        $remainder = $sum % 11;
        $checkDigit2 = 2 > $remainder ? 0 : 11 - $remainder;

        return (int) $cpf[10] === $checkDigit2;
    }
}
