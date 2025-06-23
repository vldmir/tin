<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

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
     * CPF Pattern: 999.999.999-99 (11 digits)
     * @var string
     */
    public const PATTERN_CPF = '^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$';

    /**
     * CNPJ Pattern: 99.999.999/9999-99 (14 digits)
     * @var string
     */
    public const PATTERN_CNPJ = '^\d{2}\.?\d{3}\.?\d{3}/?\d{4}-?\d{2}$';

    /**
     * Combined pattern for all types
     * @var string
     */
    public const PATTERN = '^(\d{3}\.?\d{3}\.?\d{3}-?\d{2}|\d{2}\.?\d{3}\.?\d{3}/?\d{4}-?\d{2})$';

    /**
     * @var int
     */
    public const LENGTH = 14; // Maximum length (CNPJ)

    /**
     * @var string
     */
    public const MASK = '999.999.999-99'; // Default to CPF

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, self::PATTERN);
    }

    protected function hasValidLength(string $tin): bool
    {
        $normalizedTin = preg_replace('/[^0-9]/', '', $tin);
        $length = strlen($normalizedTin);
        return $length === 11 || $length === 14; // CPF or CNPJ
    }

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
     * Validate CPF checksum.
     */
    private function isValidCPF(string $cpf): bool
    {
        // Calculate first check digit
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += ((int) $cpf[$i]) * (10 - $i);
        }
        
        $remainder = $sum % 11;
        $checkDigit1 = $remainder < 2 ? 0 : 11 - $remainder;
        
        if ($checkDigit1 != (int) $cpf[9]) {
            return false;
        }
        
        // Calculate second check digit
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += ((int) $cpf[$i]) * (11 - $i);
        }
        
        $remainder = $sum % 11;
        $checkDigit2 = $remainder < 2 ? 0 : 11 - $remainder;
        
        return $checkDigit2 == (int) $cpf[10];
    }

    /**
     * Validate CNPJ checksum.
     */
    private function isValidCNPJ(string $cnpj): bool
    {
        // Weights for first check digit
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        
        // Calculate first check digit
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ((int) $cnpj[$i]) * $weights1[$i];
        }
        
        $remainder = $sum % 11;
        $checkDigit1 = $remainder < 2 ? 0 : 11 - $remainder;
        
        if ($checkDigit1 != (int) $cnpj[12]) {
            return false;
        }
        
        // Weights for second check digit
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        
        // Calculate second check digit
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += ((int) $cnpj[$i]) * $weights2[$i];
        }
        
        $remainder = $sum % 11;
        $checkDigit2 = $remainder < 2 ? 0 : 11 - $remainder;
        
        return $checkDigit2 == (int) $cnpj[13];
    }

    /**
     * Get input mask based on the TIN type.
     */
    public function getInputMask(): string
    {
        // Default to CPF format
        return '999.999.999-99';
    }

    /**
     * Get placeholder based on the TIN type.
     */
    public function getPlaceholder(): string
    {
        return '123.456.789-09';
    }

    /**
     * Get all TIN types supported by Brazil.
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
     * Identify the TIN type for a given Brazilian TIN.
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
     * Format input according to TIN type.
     */
    public function formatInput(string $input): string
    {
        $normalized = preg_replace('/[^0-9]/', '', $input);
        
        if (strlen($normalized) === 0) {
            return '';
        }
        
        // Determine format based on length
        if (strlen($normalized) <= 11) {
            // Format as CPF: 999.999.999-99
            $result = '';
            for ($i = 0; $i < strlen($normalized) && $i < 11; $i++) {
                if ($i === 3 || $i === 6) {
                    $result .= '.';
                } elseif ($i === 9) {
                    $result .= '-';
                }
                $result .= $normalized[$i];
            }
            return $result;
        } else {
            // Format as CNPJ: 99.999.999/9999-99
            $result = '';
            for ($i = 0; $i < strlen($normalized) && $i < 14; $i++) {
                if ($i === 2 || $i === 5) {
                    $result .= '.';
                } elseif ($i === 8) {
                    $result .= '/';
                } elseif ($i === 12) {
                    $result .= '-';
                }
                $result .= $normalized[$i];
            }
            return $result;
        }
    }
} 