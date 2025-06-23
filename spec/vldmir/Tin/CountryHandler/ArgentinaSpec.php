<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class ArgentinaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '20123456780'; // Invalid checksum

    public const INVALID_NUMBER_LENGTH = '2012345678'; // Too short

    public const INVALID_NUMBER_PATTERN = 'AB-CDEFGHIJ-K';

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '19-12345678-9', // Invalid type prefix (19)
        '35-12345678-9', // Invalid type prefix (35)
        '20-12345678-0', // Invalid checksum
        '30-71234567-8', // Invalid checksum
    ];

    public const VALID_NUMBER = [
        // Valid CUIT numbers with correct checksums
        '20-12345678-9',
        '20123456789',
        '27-34567890-1',
        '27345678901',
        '30-71234567-9',
        '30712345679',
        '33-50000001-9',
        '33500000019',
    ];
}
