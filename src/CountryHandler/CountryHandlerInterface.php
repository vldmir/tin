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
 * Determines the type of a given Tax Identification Number (TIN).
 *
 * @param string $tin The TIN to analyze.
 * @return array{code: string, name: string, description?: string}|null An associative array with TIN type details if identifiable, or null if the type cannot be determined.
 */
    public function identifyTinType(string $tin): ?array;

    /**
 * Determines if the handler supports TIN validation for the specified country code.
 *
 * @param string $country The country code to check.
 * @return bool True if TIN validation is supported for the country, false otherwise.
 */
    public static function supports(string $country): bool;

    /**
 * Validates the provided Tax Identification Number (TIN).
 *
 * @param string $tin The TIN to validate.
 * @return bool True if the TIN is valid.
 * @throws TINException If the TIN is invalid.
 */
    public function validate(string $tin): bool;
}
