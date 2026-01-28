<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class IndonesiaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '001234567890123'; // Starts with 00

    public const INVALID_NUMBER_LENGTH = '01234567890123'; // Too short (14 digits)

    // No INVALID_NUMBER_PATTERN - pattern check is same as length check for digit-only format

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '000000000000000', // All zeros
        '00.123.456.7-890.12', // Invalid tax office code (00)
    ];

    public const VALID_NUMBER = [
        // Valid NPWP numbers (15 digits)
        '01.234.567.8-901.234',
        '012345678901234',
        '12.345.678.9-012.345',
        '123456789012345',
        '31.000.000.1-000.000',
        '310000001000000',
    ];
}
