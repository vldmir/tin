<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class SwitzerlandSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '756.1234.5678.90'; // Invalid AVS checksum (correct is 97)

    public const INVALID_NUMBER_LENGTH = '756.1234.567'; // Too short

    // No INVALID_NUMBER_PATTERN - after normalization, pattern check is same as length + prefix check

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '755.1234.5678.97', // Doesn't start with 756
        '756.1234.5678.99', // Invalid AVS checksum
        'CHE-123.456.780',  // Invalid UID checksum
    ];

    public const VALID_NUMBER = [
        // Valid AVS/AHV numbers with correct EAN-13 checksum
        '756.1234.5678.97',
        '7561234567897',
        '756.9999.9999.91',
        '756.1111.1111.13',

        // Valid UID numbers with correct modulo 11 checksum
        'CHE-123.456.788',
        'CHE123456788',
        'CHE-100.000.006',
        'CHE100000006',
    ];
}
