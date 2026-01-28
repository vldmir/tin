<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class UnitedStatesSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '070000000'; // Invalid EIN prefix (07) + zeros in serial

    public const INVALID_NUMBER_LENGTH = '12345678901'; // Too long

    // No INVALID_NUMBER_PATTERN - pattern is just 9 digits after normalization

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '000000000', // All zeros
        '666123456', // Invalid area code 666
        '123004567', // Zero group
        '456780000', // Zero serial
    ];

    public const VALID_NUMBER = [
        // Valid SSN formats (non-blocked area codes)
        '001-23-4567',
        '001234567',
        '078-05-1120',

        // Valid ITIN formats (starts with 9, fourth-fifth digits in valid ranges 50-65, 70-88, 90-92, 94-99)
        '900-70-1234',
        '900701234',
        '912-88-5678',

        // Valid EIN formats (valid prefixes like 12, 52, 91, 98)
        '121234567',
        '521234567',
        '911234567',
    ];
}
