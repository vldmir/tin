<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class SouthKoreaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '900101-1234560'; // Invalid RRN checksum

    public const INVALID_NUMBER_LENGTH = '900101-123'; // Too short

    // No INVALID_NUMBER_PATTERN - after normalization, pattern check is same as length check

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '900132-1234567', // Invalid date (32nd day)
        '901301-1234567', // Invalid month (13)
        '123-45-67890',   // Invalid BRN checksum
    ];

    public const VALID_NUMBER = [
        // Valid RRN numbers with correct checksums
        '900101-1234568',
        '9001011234568',
        '850315-2345678',
        '8503152345678',

        // Valid BRN numbers with correct checksums
        '220-86-05170',
        '2208605170',
        '123-45-67891',
        '1234567891',
    ];
}
