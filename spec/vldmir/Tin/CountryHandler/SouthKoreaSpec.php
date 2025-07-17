<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class SouthKoreaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '900101-1234560'; // Invalid RRN checksum

    public const INVALID_NUMBER_LENGTH = '900101-123'; // Too short

    public const INVALID_NUMBER_PATTERN = 'ABC-DEF-GHIJKLM';

    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '900132-1234567', // Invalid date (32nd day)
        '901301-1234567', // Invalid month (13)
        '900101-9234567', // Invalid gender digit (9 for 1900s)
        '123-45-67890',   // Invalid BRN checksum
        '000-00-00000',   // All zeros BRN
    ];

    public const VALID_NUMBER = [
        // Valid RRN numbers with correct checksums
        '900101-1234563',
        '9001011234563',
        '850315-2345674',
        '8503152345674',

        // Valid BRN numbers with correct checksums
        '220-86-05173',
        '2208605173',
        '123-45-67894',
        '1234567894',
    ];
}
