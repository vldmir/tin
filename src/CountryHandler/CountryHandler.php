<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

use loophp\Tin\Exception\TINException;

use function strlen;

abstract class CountryHandler implements CountryHandlerInterface
{
    final public static function supports(string $country): bool
    {
        return strtoupper($country) === strtoupper(static::COUNTRYCODE);
    }

    final public function validate(string $tin): bool
    {
        $normalizedTin = $this->normalizeTin($tin);

        if (!$this->hasValidLength($normalizedTin)) {
            throw TINException::invalidLength($tin);
        }

        if (!$this->hasValidPattern($normalizedTin)) {
            throw TINException::invalidPattern($tin);
        }

        if (!$this->hasValidDate($normalizedTin)) {
            throw TINException::invalidDate($tin);
        }

        if (!$this->hasValidRule($normalizedTin)) {
            throw TINException::invalidSyntax($tin);
        }

        return true;
    }

    /**
     * Get digit at a given position.
     */
    protected function digitAt(string $str, int $index): int
    {
        return (int) ($str[$index] ?? 0);
    }

    protected function digitsSum(int $int): int
    {
        return array_reduce(
            str_split((string) $int),
            static function (int $carry, string $digit): int {
                return $carry + (int) $digit;
            },
            0
        );
    }

    /**
     * Get the alphabetical position.
     *
     * eg: A = 1
     */
    protected function getAlphabeticalPosition(string $character): int
    {
        return 1 + array_flip(range('a', 'z'))[strtolower($character)];
    }

    protected function getLastDigit(int $number): int
    {
        $split = str_split((string) $number);

        return (int) end($split);
    }

    protected function hasValidDate(string $tin): bool
    {
        return true;
    }

    /**
     * Match length.
     */
    protected function hasValidLength(string $tin): bool
    {
        return $this->matchLength($tin, static::LENGTH);
    }

    protected function hasValidPattern(string $tin): bool
    {
        return $this->matchPattern($tin, static::PATTERN);
    }

    protected function hasValidRule(string $tin): bool
    {
        return true;
    }

    protected function matchLength(string $tin, int $length): bool
    {
        return strlen($tin) === $length;
    }

    protected function matchPattern(string $subject, string $pattern): bool
    {
        return 1 === preg_match(sprintf('/%s/i', $pattern), $subject);
    }

    protected function normalizeTin(string $tin): string
    {
        if (null !== $string = preg_replace('#[^[:alnum:]\-+]#u', '', $tin)) {
            return strtoupper($string);
        }

        return '';
    }

    /**
     * Get input mask for TIN format.
     * Override in child classes for custom masks.
     */
    public function getInputMask(): string
    {
        return static::MASK ?? str_repeat('9', static::LENGTH);
    }

    /**
     * Get placeholder text for TIN input.
     * Override in child classes for custom placeholders.
     */
    public function getPlaceholder(): string
    {
        $mask = $this->getInputMask();
        return str_replace(['9', 'A', 'a'], ['1', 'A', 'a'], $mask);
    }

    /**
     * Format TIN input according to mask (for display purposes).
     */
    public function formatInput(string $input): string
    {
        $mask = $this->getInputMask();
        $normalized = $this->normalizeTin($input);
        $result = '';
        $inputIndex = 0;
        
        for ($i = 0; $i < strlen($mask) && $inputIndex < strlen($normalized); $i++) {
            $maskChar = $mask[$i];
            $inputChar = $normalized[$inputIndex] ?? '';
            
            if ($maskChar === '9') {
                if (ctype_digit($inputChar)) {
                    $result .= $inputChar;
                    $inputIndex++;
                } else {
                    break;
                }
            } elseif ($maskChar === 'A') {
                if (ctype_alpha($inputChar)) {
                    $result .= strtoupper($inputChar);
                    $inputIndex++;
                } else {
                    break;
                }
            } elseif ($maskChar === 'a') {
                if (ctype_alpha($inputChar)) {
                    $result .= strtolower($inputChar);
                    $inputIndex++;
                } else {
                    break;
                }
            } else {
                // Separator character (space, dash, etc.)
                $result .= $maskChar;
            }
        }
        
        return $result;
    }
}
