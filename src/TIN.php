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
     * Country code aliases mapping (ISO 3166-1 alpha-2 to internal codes).
     *
     * @var array<string, string>
     */
    private static $countryAliases = [
        'GB' => 'UK', // Great Britain -> United Kingdom
    ];

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
     * @throws TINException
     */
    public function check(bool $strict = false): bool
    {
        $parsedTin = $this->parse($this->slug, $strict);

        return $this->getAlgorithm($parsedTin['country'])->validate($parsedTin['tin']);
    }

    /**
     * Format input according to TIN mask.
     *
     * @throws TINException
     */
    public function formatInput(string $input): string
    {
        $parsedTin = $this->parse($this->slug, false);
        $handler = $this->getAlgorithm($parsedTin['country']);

        return $handler->formatInput($input);
    }

    public static function from(string $countryCode, string $tin): TIN
    {
        // Normalize country code (e.g., GB -> UK)
        $normalizedCountry = self::normalizeCountryCode($countryCode);
        // Normalize TIN to remove spaces and other non-alphanumeric characters
        $normalizedTin = self::normalizeTin($tin);

        return self::fromSlug($normalizedCountry . $normalizedTin);
    }

    private static function fromSlug(string $slug): TIN
    {
        return new self($slug);
    }

    /**
     * Get input mask for the TIN country.
     *
     * @throws TINException
     */
    public function getInputMask(): string
    {
        $parsedTin = $this->parse($this->slug, false);
        $handler = $this->getAlgorithm($parsedTin['country']);

        return $handler->getInputMask();
    }

    /**
     * Get mask and placeholder for a specific country.
     */
    public static function getMaskForCountry(string $countryCode): array
    {
        $normalizedCode = self::normalizeCountryCode($countryCode);

        // Check if country is supported first
        if (!self::isCountrySupported($normalizedCode)) {
            throw TINException::invalidCountry($countryCode);
        }

        // Create handler directly without TIN validation
        foreach (self::$algorithms as $algorithm) {
            if ($algorithm::supports($normalizedCode)) {
                $handler = new $algorithm();

                return [
                    'mask' => $handler->getInputMask(),
                    'placeholder' => $handler->getPlaceholder(),
                    'country' => $normalizedCode,
                ];
            }
        }

        throw TINException::invalidCountry($countryCode);
    }

    /**
     * Get placeholder text for the TIN country.
     *
     * @throws TINException
     */
    public function getPlaceholder(): string
    {
        $parsedTin = $this->parse($this->slug, false);
        $handler = $this->getAlgorithm($parsedTin['country']);

        return $handler->getPlaceholder();
    }

    /**
     * Get all supported countries.
     *
     * @return array<string> Array of country codes
     */
    public static function getSupportedCountries(): array
    {
        return array_keys(self::$algorithms);
    }

    /**
     * Get all supported countries with additional information.
     *
     * @return array<string, array> Array of countries with their details
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
     * Get all TIN types supported by this TIN instance's country.
     *
     * @throws TINException
     */
    public function getTinTypes(): array
    {
        $parsedTin = $this->parse($this->slug, false);
        $handler = $this->getAlgorithm($parsedTin['country']);

        return $handler->getTinTypes();
    }

    /**
     * Get all TIN types for a specific country.
     *
     * @throws TINException
     */
    public static function getTinTypesForCountry(string $countryCode): array
    {
        $normalizedCode = self::normalizeCountryCode($countryCode);

        // Check if country is supported first
        if (!self::isCountrySupported($normalizedCode)) {
            throw TINException::invalidCountry($countryCode);
        }

        // Create handler directly without TIN validation
        foreach (self::$algorithms as $algorithm) {
            if ($algorithm::supports($normalizedCode)) {
                $handler = new $algorithm();

                return $handler->getTinTypes();
            }
        }

        throw TINException::invalidCountry($countryCode);
    }

    /**
     * Identify the TIN type for this TIN instance.
     *
     * @throws TINException
     *
     * @return array{code: string, name: string, description?: string}|null
     */
    public function identifyTinType(): ?array
    {
        $parsedTin = $this->parse($this->slug, false);
        $handler = $this->getAlgorithm($parsedTin['country']);

        return $handler->identifyTinType($parsedTin['tin']);
    }

    public static function isCountrySupported(string $countryCode): bool
    {
        $normalizedCode = self::normalizeCountryCode($countryCode);

        foreach (self::$algorithms as $algorithm) {
            if (true === $algorithm::supports($normalizedCode)) {
                return true;
            }
        }

        return false;
    }

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

    /**
     * Normalize country code using aliases.
     */
    private static function normalizeCountryCode(string $countryCode): string
    {
        $code = strtoupper($countryCode);

        return self::$countryAliases[$code] ?? $code;
    }

    private static function normalizeTin(string $tin): string
    {
        if (null !== $string = preg_replace('#[^[:alnum:]]#u', '', $tin)) {
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
