<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class SwitzerlandSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '756.1234.5678.91'; // Invalid AVS checksum

    public const INVALID_NUMBER_LENGTH = '756.1234.567'; // Too short

    public const INVALID_NUMBER_PATTERN = 'ABC.DEFG.HIJK.LM';

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '755.1234.5678.90', // Doesn't start with 756
        '756.1234.5678.99', // Invalid AVS checksum
        'CHE-123.456.780',  // Invalid UID checksum
        'CHE-999.999.999',  // Invalid UID (checksum would be 10)
    ];

    public const VALID_NUMBER = [
        // Valid AVS/AHV numbers with correct EAN-13 checksum
        '756.1234.5678.90',
        '756.9999.9999.99',
        '756.1111.1111.10',

        // Valid UID numbers with correct modulo 11 checksum
        'CHE-123.456.789',
        'CHE123456789',
        'CHE-100.000.008',
        'CHE100000008',
    ];
}
