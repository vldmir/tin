<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class ChinaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '110105199012310021'; // Invalid checksum

    public const INVALID_NUMBER_LENGTH = '11010519901231002'; // Too short

    public const INVALID_NUMBER_PATTERN = 'ABCDEFGHIJKLMNOPQR';

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '110105190013310023', // Invalid month (13)
        '110105199012320023', // Invalid day (32)
        '990105199012310023', // Invalid region code (99)
        '11010519901231002Y', // Invalid checksum character
        '91110108MA01A3F52Z', // Invalid business code (contains Z)
    ];

    public const VALID_NUMBER = [
        // Valid personal ID numbers with correct checksums
        '11010519491231002X',
        '110105199012310027',
        '440524198001010013',
        '310110199012310025',

        // Valid business codes with correct checksums
        '91110108MA01A3F52G',
        '91440300MA5G3TJX4D',
        '91310000MA1FL0P64U',
    ];
}
