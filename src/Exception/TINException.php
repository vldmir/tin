<?php

declare(strict_types=1);

namespace vldmir\Tin\Exception;

use Exception;

use function sprintf;

/**
 * Base class for all exceptions.
 */
final class TINException extends Exception
{
    public static function emptySlug(): TINException
    {
        return new self('Invalid Slug. Reason: Void string.');
    }

    public static function invalidCountry(string $countryCode): TINException
    {
        return new self(sprintf('No handler available for this country code: %s.', $countryCode));
    }

    public static function invalidDate(string $tin): TINException
    {
        return new self(sprintf('Invalid TIN(%s). Reason: Invalid date.', $tin));
    }

    public static function invalidLength(string $tin): TINException
    {
        return new self(sprintf('Invalid TIN(%s). Reason: Invalid length.', $tin));
    }

    public static function invalidPattern(string $tin): TINException
    {
        return new self(sprintf('Invalid TIN(%s). Reason: Invalid pattern.', $tin));
    }

    public static function invalidSyntax(string $tin): TINException
    {
        return new self(sprintf('Invalid TIN(%s). Reason: Invalid syntax.', $tin));
    }
}
