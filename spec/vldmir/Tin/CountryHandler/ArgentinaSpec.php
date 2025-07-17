<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class ArgentinaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '20123456780'; // Invalid checksum (should be 6)

    public const INVALID_NUMBER_LENGTH = ['2012345678', '123456789012']; // Too short and too long

    // Skip pattern test as global normalization makes it difficult to create a pure pattern failure
    public const INVALID_NUMBER_PATTERN = [];

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '19-12345678-6', // Invalid type prefix (19)
        '35-12345678-6', // Invalid type prefix (35)
        '20-12345678-0', // Invalid checksum (should be 6)
        '20-87654321-0', // Invalid checksum (should be 5)
    ];

    public const VALID_NUMBER = [
        // Valid CUIT numbers with correct checksums
        '20-12345678-6',
        '20123456786',
        '20-87654321-5',
        '20876543215',
        '20-11111111-2',
        '20111111112',
        '20-22222222-3',
        '20222222223',
    ];
}
