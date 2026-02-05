<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class RussiaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '123456789011'; // Invalid personal INN checksum

    public const INVALID_NUMBER_LENGTH = '12345678'; // Too short

    public const INVALID_NUMBER_PATTERN = 'ABCDEFGHIJKL';

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '001234567890', // Invalid region (00)
        '991234567890', // Valid region but invalid checksum
        '0012345678',   // Invalid company region (00)
        '9912345678',   // Valid region but invalid checksum
    ];

    public const VALID_NUMBER = [
        // Valid personal INN (12 digits)
        '500100732259',
        '773399405589',
        '123456789047',

        // Valid company INN (10 digits)
        '7707083893',
        '5024057742',
        '7736050003',
    ];
}
