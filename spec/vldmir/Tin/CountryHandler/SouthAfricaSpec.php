<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class SouthAfricaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '0123456788'; // Invalid Luhn checksum

    public const INVALID_NUMBER_LENGTH = '012345678'; // Too short

    public const INVALID_NUMBER_PATTERN = 'ABCDEFGHIJ';

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '4123456789', // Doesn't start with 0,1,2,3,9
        '0000000000', // All same digits
        '1111111111', // All same digits
        '0123456789', // Invalid Luhn checksum
    ];

    public const VALID_NUMBER = [
        // Valid TIN numbers with correct Luhn checksums
        '0001339050',
        '1234567897',
        '2345678906',
        '3456789015',
        '9012345640',
    ];
}
