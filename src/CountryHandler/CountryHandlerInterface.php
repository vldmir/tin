<?php

declare(strict_types=1);

namespace vldmir\Tin\CountryHandler;

use vldmir\Tin\Exception\TINException;

/**
 * Base interface for a validation algorithm, as used in TINValid class.
 */
interface CountryHandlerInterface
{
    /**
     * @var string
     */
    public const COUNTRYCODE = 'UNKNOWN';

    /**
     * @var int
     */
    public const LENGTH = 0;

    /**
     * @var string
     */
    public const MASK = '';

    /**
     * @var string
     */
    public const PATTERN = '';

    /**
     * Get input mask for TIN format.
     *
     * @return string
     *   Input mask (e.g., "999999999" or "AA999999A")
     */
    public function getInputMask(): string;

    /**
     * Get placeholder text for TIN input.
     *
     * @return string
     *   Placeholder text (e.g., "123456789")
     */
    public function getPlaceholder(): string;

    /**
     * Get all TIN types supported by this country.
     *
     * @return array<int, array{code: string, name: string, description?: string}>
     *   Array of TIN types indexed by pattern number
     */
    public function getTinTypes(): array;

    /**
     * Identify the TIN type for a given TIN.
     *
     * @param string $tin
     *   The TIN to identify
     *
     * @return array{code: string, name: string, description?: string}|null
     *   The TIN type information or null if not identifiable
     */
    public function identifyTinType(string $tin): ?array;

    /**
     * Check if the algorithm supports the TIN.
     *
     * @param string $country
     *   The TIN.
     *
     * @return bool
     *   True if it supports it, false otherwise.
     */
    public static function supports(string $country): bool;

    /**
     * Validate a tin number.
     *
     * @throws TINException
     */
    public function validate(string $tin): bool;
}
