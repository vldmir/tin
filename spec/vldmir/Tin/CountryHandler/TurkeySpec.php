<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class TurkeySpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '12345678901'; // Invalid checksum

    public const INVALID_NUMBER_LENGTH = '123456789'; // Too short

    public const INVALID_NUMBER_PATTERN = 'ABCDEFGHIJK';

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '01234567890', // Personal ID starting with 0
        '11111111111', // All same digits
        '0000000000',  // All zeros business ID
        '10000000145', // Invalid personal checksum
        '1234567891',  // Invalid business checksum
    ];

    public const VALID_NUMBER = [
        // Valid T.C. Kimlik No (11 digits)
        '10000000146',
        '14702551584',
        '38246312956',

        // Valid Vergi Kimlik No (10 digits)
        '1234567890',
        '8770385074',
        '4840327613',
    ];
}
