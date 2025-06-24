<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use vldmir\Tin\Exception\TINException;

use function sprintf;
use function strlen;

abstract class CountryHandler implements CountryHandlerInterface
{
    /**
     * Formats a TIN input string according to the country-specific input mask for display purposes.
     *
     * The input is normalized and then formatted by applying the mask, inserting separators and enforcing digit or letter requirements as specified by the mask.
     *
     * @param string $input The raw TIN input to be formatted.
     * @return string The formatted TIN string.
     */
    public function formatInput(string $input): string
    {
        $mask = $this->getInputMask();
        $normalized = $this->normalizeTin($input);
        $result = '';
        $inputIndex = 0;

        for ($i = 0; strlen($mask) > $i && strlen($normalized) > $inputIndex; ++$i) {
            $maskChar = $mask[$i];
            $inputChar = $normalized[$inputIndex] ?? '';

            if ('9' === $maskChar) {
                if (ctype_digit($inputChar)) {
                    $result .= $inputChar;
                    ++$inputIndex;
                } else {
                    break;
                }
            } elseif ('A' === $maskChar) {
                if (ctype_alpha($inputChar)) {
                    $result .= strtoupper($inputChar);
                    ++$inputIndex;
                } else {
                    break;
                }
            } elseif ('a' === $maskChar) {
                if (ctype_alpha($inputChar)) {
                    $result .= strtolower($inputChar);
                    ++$inputIndex;
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

    /**
     * Returns the input mask used for formatting TINs.
     *
     * By default, returns the value of the `MASK` constant if defined; otherwise, returns a string of '9's with a length equal to the `LENGTH` constant. Intended to be overridden in subclasses for country-specific masks.
     *
     * @return string The input mask for TIN formatting.
     */
    public function getInputMask(): string
    {
        return static::MASK ?? str_repeat('9', static::LENGTH);
    }

    /**
     * Returns a placeholder string for TIN input based on the input mask.
     *
     * Replaces mask characters with representative placeholder characters to guide user input.
     * Can be overridden in subclasses for custom placeholder formats.
     *
     * @return string The placeholder string for TIN input.
     */
    public function getPlaceholder(): string
    {
        $mask = $this->getInputMask();

        return str_replace(['9', 'A', 'a'], ['1', 'A', 'a'], $mask);
    }

    /**
     * Returns an array of TIN types supported by the country.
     *
     * The default implementation provides a single TIN type with code 'TIN', name 'Tax Identification Number', and a description including the country code.
     *
     * @return array An array of TIN type definitions.
     */
    public function getTinTypes(): array
    {
        return [
            1 => [
                'code' => 'TIN',
                'name' => 'Tax Identification Number',
                'description' => 'Standard tax identification number for ' . static::COUNTRYCODE,
            ],
        ];
    }

    /**
     * Determines the TIN type for the provided TIN value.
     *
     * Returns the first TIN type from `getTinTypes()` if the normalized TIN matches the valid pattern; otherwise, returns null.
     *
     * @param string $tin The Tax Identification Number to identify.
     * @return array|null The TIN type information, or null if the TIN does not match the expected pattern.
     */
    public function identifyTinType(string $tin): ?array
    {
        $normalizedTin = $this->normalizeTin($tin);

        if ($this->hasValidPattern($normalizedTin)) {
            $types = $this->getTinTypes();

            return $types[1] ?? null;
        }

        return null;
    }

    /**
     * Determines if the handler supports the specified country code.
     *
     * @param string $country The country code to check.
     * @return bool True if the handler supports the given country code; otherwise, false.
     */
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

    /**
     * Normalizes a TIN by removing all non-alphanumeric characters and converting it to uppercase.
     *
     * @param string $tin The input Tax Identification Number.
     * @return string The normalized TIN, or an empty string if normalization fails.
     */
    protected function normalizeTin(string $tin): string
    {
        if (null !== $string = preg_replace('#[^[:alnum:]]#u', '', $tin)) {
            return strtoupper($string);
        }

        return '';
    }
}
