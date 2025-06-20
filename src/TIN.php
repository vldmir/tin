<?php

declare(strict_types=1);

namespace loophp\Tin;

use loophp\Tin\CountryHandler\Austria;
use loophp\Tin\CountryHandler\Belgium;
use loophp\Tin\CountryHandler\Bulgaria;
use loophp\Tin\CountryHandler\CountryHandlerInterface;
use loophp\Tin\CountryHandler\Croatia;
use loophp\Tin\CountryHandler\Cyprus;
use loophp\Tin\CountryHandler\CzechRepublic;
use loophp\Tin\CountryHandler\Denmark;
use loophp\Tin\CountryHandler\Estonia;
use loophp\Tin\CountryHandler\Finland;
use loophp\Tin\CountryHandler\France;
use loophp\Tin\CountryHandler\Germany;
use loophp\Tin\CountryHandler\Greece;
use loophp\Tin\CountryHandler\Hungary;
use loophp\Tin\CountryHandler\Ireland;
use loophp\Tin\CountryHandler\Italy;
use loophp\Tin\CountryHandler\Latvia;
use loophp\Tin\CountryHandler\Lithuania;
use loophp\Tin\CountryHandler\Luxembourg;
use loophp\Tin\CountryHandler\Malta;
use loophp\Tin\CountryHandler\Netherlands;
use loophp\Tin\CountryHandler\Poland;
use loophp\Tin\CountryHandler\Portugal;
use loophp\Tin\CountryHandler\Romania;
use loophp\Tin\CountryHandler\Slovakia;
use loophp\Tin\CountryHandler\Slovenia;
use loophp\Tin\CountryHandler\Spain;
use loophp\Tin\CountryHandler\Sweden;
use loophp\Tin\CountryHandler\UnitedKingdom;
use loophp\Tin\Exception\TINException;

/**
 * The main class to validate TIN numbers.
 */
final class TIN
{
    /**
     * @var array<string, class-string>
     */
    private static $algorithms = [
        'AT' => Austria::class,
        'BE' => Belgium::class,
        'BG' => Bulgaria::class,
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
        'IE' => Ireland::class,
        'IT' => Italy::class,
        'LT' => Lithuania::class,
        'LU' => Luxembourg::class,
        'LV' => Latvia::class,
        'MT' => Malta::class,
        'NL' => Netherlands::class,
        'PL' => Poland::class,
        'PT' => Portugal::class,
        'RO' => Romania::class,
        'SE' => Sweden::class,
        'SI' => Slovenia::class,
        'SK' => Slovakia::class,
        'UK' => UnitedKingdom::class,
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

    public static function from(string $countryCode, string $tin): TIN
    {
        return self::fromSlug($countryCode . $tin);
    }

    public static function fromSlug(string $slug): TIN
    {
        return new self($slug);
    }

    public static function isCountrySupported(string $countryCode): bool
    {
        foreach (self::$algorithms as $algorithm) {
            if (true === $algorithm::supports($countryCode)) {
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

    /**
     * Get mask and placeholder for a specific country.
     */
    public static function getMaskForCountry(string $countryCode): array
    {
        // Use a dummy TIN to create the instance
        $tin = self::from($countryCode, '123456789');
        
        try {
            return [
                'mask' => $tin->getInputMask(),
                'placeholder' => $tin->getPlaceholder(),
                'country' => $countryCode,
            ];
        } catch (TINException $e) {
            throw TINException::invalidCountry($countryCode);
        }
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
