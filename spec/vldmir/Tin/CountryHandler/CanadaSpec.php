<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class CanadaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '000000000'; // Invalid BN - all zeros

    public const INVALID_NUMBER_LENGTH = '12345678'; // Too short

    // No INVALID_NUMBER_PATTERN - alphanumeric input becomes empty or short after normalization

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '000-000-000', // Invalid start with 0 (SIN)
        '800-000-000', // Invalid start with 8 (SIN)
        '900-000-000', // Invalid start with 9 (SIN)
    ];

    public const VALID_NUMBER = [
        // Valid SIN numbers (pass Luhn algorithm)
        '130-692-544',
        '130692544',
        '123-456-782',
        '123456782',
        '234-567-897',
        '234567897',

        // Valid BN numbers with RC suffix (unambiguous)
        '123456789RC0001',
        '123456789 RC0001',
    ];
}
