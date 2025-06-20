<?php

declare(strict_types=1);

namespace loophp\Tin\CountryHandler;

use loophp\Tin\Exception\TINException;

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
    public const PATTERN = '';

    /**
     * @var string
     */
    public const MASK = '';

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
}
