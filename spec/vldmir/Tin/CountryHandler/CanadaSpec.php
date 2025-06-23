<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class CanadaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '123456789'; // Invalid SIN (fails Luhn)

    public const INVALID_NUMBER_LENGTH = '12345678'; // Too short

    public const INVALID_NUMBER_PATTERN = 'ABC-DEF-GHI';

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '000-000-000', // Invalid start with 0
        '800-000-000', // Invalid start with 8
        '900-000-000', // Invalid start with 9
        '123-456-789', // Fails Luhn check
        '000000000',   // All zeros BN
    ];

    public const VALID_NUMBER = [
        // Valid SIN numbers (pass Luhn algorithm)
        '130-692-544',
        '130692544',
        '123-456-782',
        '123456782',
        '234-567-897',
        '234567897',

        // Valid BN numbers
        '123456789',
        '123456789RC0001', // BN with program account
        '123456789 RC0001',
        '987654321',
    ];
}
