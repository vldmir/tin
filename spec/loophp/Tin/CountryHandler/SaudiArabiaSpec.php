<?php

declare(strict_types=1);

namespace spec\loophp\Tin\CountryHandler;

use tests\loophp\Tin\AbstractAlgorithmSpec;

class SaudiArabiaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '300123456789014'; // Invalid checksum

    public const INVALID_NUMBER_LENGTH = '30012345678901'; // Too short

    public const INVALID_NUMBER_PATTERN = 'ABCDEFGHIJKLMNO';

    public const VALID_NUMBER = [
        // Valid TIN numbers with correct checksums
        '300123456789015',
        '301234567890126',
        '310000000000015',
        '399999999999990',
    ];
    
    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '200123456789015', // Doesn't start with 3
        '333333333333333', // All same digits
        '300123456789016', // Invalid checksum
        '400123456789015', // Doesn't start with 3
    ];
} 