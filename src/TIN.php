<?php

declare(strict_types=1);

namespace vldmir\Tin;

use Exception;
use vldmir\Tin\CountryHandler\Argentina;
use vldmir\Tin\CountryHandler\Australia;
use vldmir\Tin\CountryHandler\Austria;
use vldmir\Tin\CountryHandler\Belgium;
use vldmir\Tin\CountryHandler\Brazil;
use vldmir\Tin\CountryHandler\Bulgaria;
use vldmir\Tin\CountryHandler\Canada;
use vldmir\Tin\CountryHandler\China;
use vldmir\Tin\CountryHandler\CountryHandlerInterface;
use vldmir\Tin\CountryHandler\Croatia;
use vldmir\Tin\CountryHandler\Cyprus;
use vldmir\Tin\CountryHandler\CzechRepublic;
use vldmir\Tin\CountryHandler\Denmark;
use vldmir\Tin\CountryHandler\Estonia;
use vldmir\Tin\CountryHandler\Finland;
use vldmir\Tin\CountryHandler\France;
use vldmir\Tin\CountryHandler\Germany;
use vldmir\Tin\CountryHandler\Greece;
use vldmir\Tin\CountryHandler\Hungary;
use vldmir\Tin\CountryHandler\India;
use vldmir\Tin\CountryHandler\Indonesia;
use vldmir\Tin\CountryHandler\Ireland;
use vldmir\Tin\CountryHandler\Italy;
use vldmir\Tin\CountryHandler\Japan;
use vldmir\Tin\CountryHandler\Latvia;
use vldmir\Tin\CountryHandler\Lithuania;
use vldmir\Tin\CountryHandler\Luxembourg;
use vldmir\Tin\CountryHandler\Malta;
use vldmir\Tin\CountryHandler\Mexico;
use vldmir\Tin\CountryHandler\Netherlands;
use vldmir\Tin\CountryHandler\Nigeria;
use vldmir\Tin\CountryHandler\Poland;
use vldmir\Tin\CountryHandler\Portugal;
use vldmir\Tin\CountryHandler\Romania;
use vldmir\Tin\CountryHandler\Russia;
use vldmir\Tin\CountryHandler\SaudiArabia;
use vldmir\Tin\CountryHandler\Slovakia;
use vldmir\Tin\CountryHandler\Slovenia;
use vldmir\Tin\CountryHandler\SouthAfrica;
use vldmir\Tin\CountryHandler\SouthKorea;
use vldmir\Tin\CountryHandler\Spain;
use vldmir\Tin\CountryHandler\Sweden;
use vldmir\Tin\CountryHandler\Switzerland;
use vldmir\Tin\CountryHandler\Turkey;
use vldmir\Tin\CountryHandler\Ukraine;
use vldmir\Tin\CountryHandler\UnitedKingdom;
use vldmir\Tin\CountryHandler\UnitedStates;
use vldmir\Tin\Exception\TINException;

/**
 * The main class to validate TIN numbers.
 */
final class TIN
{
    /**
     * @var array<string, class-string>
     */
    private static $algorithms = [
        'AR' => Argentina::class,
        'AT' => Austria::class,
        'AU' => Australia::class,
        'BE' => Belgium::class,
        'BG' => Bulgaria::class,
        'BR' => Brazil::class,
        'CA' => Canada::class,
        'CH' => Switzerland::class,
        'CN' => China::class,
        'CY' => Cyprus::class,
        'CZ' => CzechRepublic::class,
        'DE' => Germany::class,
        'DK' => Denmark::class,
        'EE' => Estonia::class,
        'ES' => Spain::class,
        'FI' => Finland::class,
        'FR' => France::class,
        'GR' => Greece::class,
        'HR' => Croatia::class,
        'HU' => Hungary::class,
        'ID' => Indonesia::class,
        'IE' => Ireland::class,
        'IN' => India::class,
        'IT' => Italy::class,
        'JP' => Japan::class,
        'KR' => SouthKorea::class,
        'LT' => Lithuania::class,
        'LU' => Luxembourg::class,
        'LV' => Latvia::class,
        'MT' => Malta::class,
        'MX' => Mexico::class,
        'NG' => Nigeria::class,
        'NL' => Netherlands::class,
        'PL' => Poland::class,
        'PT' => Portugal::class,
        'RO' => Romania::class,
        'RU' => Russia::class,
        'SA' => SaudiArabia::class,
        'SE' => Sweden::class,
        'SI' => Slovenia::class,
        'SK' => Slovakia::class,
        'TR' => Turkey::class,
        'UA' => Ukraine::class,
        'UK' => UnitedKingdom::class,
        'US' => UnitedStates::class,
        'ZA' => SouthAfrica::class,
    ];

    /**
     * @var string
     */
    private $slug;

    private function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * Validates the TIN for the current country using the appropriate country-specific algorithm.
     *
     * @param bool $strict Whether to enforce strict parsing of the TIN.
     * @return bool True if the TIN is valid for the country; false otherwise.
     * @throws TINException If the country is unsupported or the TIN format is invalid.
     */
    public function check(bool $strict = false): bool
    {
        $parsedTin = $this->parse($this->slug, $strict);

        return $this->getAlgorithm($parsedTin['country'])->validate($parsedTin['tin']);
    }

    /**
     * Formats the provided input string according to the TIN mask for the current country.
     *
     * @param string $input The input string to be formatted as a TIN.
     * @return string The formatted TIN string.
     * @throws TINException If the country is unsupported or formatting fails.
     */
    public function formatInput(string $input): string
    {
        $parsedTin = $this->parse($this->slug, false);
        $handler = $this->getAlgorithm($parsedTin['country']);

        return $handler->formatInput($input);
    }

    /**
     * Creates a TIN instance from a country code and TIN value.
     *
     * @param string $countryCode The ISO 2-letter country code.
     * @param string $tin The tax identification number.
     * @return TIN The constructed TIN instance.
     */
    public static function from(string $countryCode, string $tin): TIN
    {
        return self::fromSlug($countryCode . $tin);
    }

    /**
     * Creates a TIN instance from a slug string containing the country code and TIN.
     *
     * @param string $slug The slug combining the country code and TIN.
     * @return TIN The created TIN instance.
     */
    public static function fromSlug(string $slug): TIN
    {
        return new self($slug);
    }

    /**
     * Returns the input mask pattern for the TIN of the associated country.
     *
     * @return string The input mask string used for formatting TIN input.
     * @throws TINException If the country is unsupported or the handler cannot be instantiated.
     */
    public function getInputMask(): string
    {
        $parsedTin = $this->parse($this->slug, false);
        $handler = $this->getAlgorithm($parsedTin['country']);

        return $handler->getInputMask();
    }

    /**
     * Returns the input mask and placeholder for the specified country code.
     *
     * @param string $countryCode The ISO 2-letter country code.
     * @return array An associative array with keys 'mask', 'placeholder', and 'country'.
     * @throws TINException If the country is not supported.
     */
    public static function getMaskForCountry(string $countryCode): array
    {
        // Check if country is supported first
        if (!self::isCountrySupported($countryCode)) {
            throw TINException::invalidCountry($countryCode);
        }

        // Create handler directly without TIN validation
        foreach (self::$algorithms as $algorithm) {
            if ($algorithm::supports($countryCode)) {
                $handler = new $algorithm();

                return [
                    'mask' => $handler->getInputMask(),
                    'placeholder' => $handler->getPlaceholder(),
                    'country' => $countryCode,
                ];
            }
        }

        throw TINException::invalidCountry($countryCode);
    }

    /**
     * Returns the placeholder text for the TIN format of the current country.
     *
     * @return string The placeholder string for the country's TIN input.
     * @throws TINException If the country is unsupported or an error occurs retrieving the placeholder.
     */
    public function getPlaceholder(): string
    {
        $parsedTin = $this->parse($this->slug, false);
        $handler = $this->getAlgorithm($parsedTin['country']);

        return $handler->getPlaceholder();
    }

    /**
     * Returns a list of all supported country codes for TIN validation.
     *
     * @return array<string> An array of ISO 2-letter country codes.
     */
    public static function getSupportedCountries(): array
    {
        return array_keys(self::$algorithms);
    }

    /**
     * Returns detailed information for all supported countries.
     *
     * For each supported country, provides an array containing the country code, input mask, placeholder, expected TIN length, validation pattern, and supported TIN types. Countries whose handlers cannot be instantiated are skipped.
     *
     * @return array<string, array> Associative array keyed by country code, each containing country details.
     */
    public static function getSupportedCountriesWithDetails(): array
    {
        $countries = [];

        foreach (self::$algorithms as $countryCode => $algorithmClass) {
            try {
                $handler = new $algorithmClass();
                $countries[$countryCode] = [
                    'country_code' => $countryCode,
                    'mask' => $handler->getInputMask(),
                    'placeholder' => $handler->getPlaceholder(),
                    'length' => $algorithmClass::LENGTH,
                    'pattern' => $algorithmClass::PATTERN,
                    'tin_types' => $handler->getTinTypes(),
                ];
            } catch (Exception $e) {
                // Skip countries that can't be instantiated
                continue;
            }
        }

        return $countries;
    }

    /**
     * Returns all TIN types supported by the country associated with this TIN instance.
     *
     * @return array An array of TIN type definitions for the country.
     * @throws TINException If the country is invalid or unsupported.
     */
    public function getTinTypes(): array
    {
        $parsedTin = $this->parse($this->slug, false);
        $handler = $this->getAlgorithm($parsedTin['country']);

        return $handler->getTinTypes();
    }

    /**
     * Returns all supported TIN types for the specified country.
     *
     * @param string $countryCode The ISO 2-letter country code.
     * @return array An array of TIN types supported by the country.
     * @throws TINException If the country is not supported.
     */
    public static function getTinTypesForCountry(string $countryCode): array
    {
        // Check if country is supported first
        if (!self::isCountrySupported($countryCode)) {
            throw TINException::invalidCountry($countryCode);
        }

        // Create handler directly without TIN validation
        foreach (self::$algorithms as $algorithm) {
            if ($algorithm::supports($countryCode)) {
                $handler = new $algorithm();

                return $handler->getTinTypes();
            }
        }

        throw TINException::invalidCountry($countryCode);
    }

    /****
     * Determines the type of TIN for the current instance using the country-specific handler.
     *
     * @throws TINException If the country is unsupported or the TIN cannot be parsed.
     *
     * @return array{code: string, name: string, description?: string}|null An associative array describing the TIN type, or null if the type cannot be identified.
     */
    public function identifyTinType(): ?array
    {
        $parsedTin = $this->parse($this->slug, false);
        $handler = $this->getAlgorithm($parsedTin['country']);

        return $handler->identifyTinType($parsedTin['tin']);
    }

    /**
     * Determines if the specified country code is supported for TIN validation.
     *
     * @param string $countryCode The ISO 2-letter country code to check.
     * @return bool True if the country is supported; false otherwise.
     */
    public static function isCountrySupported(string $countryCode): bool
    {
        foreach (self::$algorithms as $algorithm) {
            if (true === $algorithm::supports($countryCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines if the TIN is valid for its country.
     *
     * Returns true if the TIN passes validation; otherwise, returns false. Does not throw exceptions on invalid input.
     *
     * @param bool $strict Whether to use strict validation rules.
     * @return bool True if the TIN is valid, false otherwise.
     */
    public function isValid(bool $strict = false): bool
    {
        try {
            $this->check($strict);
        } catch (TINException $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws TINException
     */
    private function getAlgorithm(string $country): CountryHandlerInterface
    {
        foreach (self::$algorithms as $algorithm) {
            if (true === $algorithm::supports($country)) {
                $handler = new $algorithm();

                if ($handler instanceof CountryHandlerInterface) {
                    return $handler;
                }
            }
        }

        throw TINException::invalidCountry($country);
    }

    private function normalizeTin(string $tin): string
    {
        if (null !== $string = preg_replace('#[^[:alnum:]\-+]#u', '', $tin)) {
            return strtoupper($string);
        }

        return '';
    }

    /**
     * @throws TINException
     *
     * @return non-empty-array<'country'|'tin', string>
     */
    private function parse(string $slug, bool $strict): array
    {
        if ('' === $slug) {
            throw TINException::emptySlug();
        }

        [$country, $tin] = sscanf($slug, '%2s%s') + ['', ''];

        return [
            'country' => (string) $country,
            'tin' => true === $strict ? (string) $tin : $this->normalizeTin((string) $tin),
        ];
    }
}
