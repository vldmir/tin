<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class IndiaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = 'ABCDE1234F'; // Invalid holder type (E)

    public const INVALID_NUMBER_LENGTH = 'AFZPK719'; // Too short

    public const INVALID_NUMBER_PATTERN = '1234567890';

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        'AAAAE1234F', // Invalid holder type (E)
        'AAAKI1234F', // Invalid holder type (I)
        'AAA1P1234K', // Digit in wrong position
        'AAAPK12345', // Last character must be letter
    ];

    public const VALID_NUMBER = [
        // Valid PAN numbers with different holder types
        'AFZPK7190K', // Individual (P)
        'AAACL1234C', // Company (C)
        'AAAFT1234B', // Firm (F)
        'AAAHH1234D', // HUF (H)
        'AAATT5678E', // Trust (T)
        'AAABG9012F', // Government (G)
    ];
}
