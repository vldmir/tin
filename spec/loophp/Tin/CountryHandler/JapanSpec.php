<?php

declare(strict_types=1);

namespace spec\loophp\Tin\CountryHandler;

use tests\loophp\Tin\AbstractAlgorithmSpec;

class JapanSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '123456789011'; // Invalid My Number checksum

    public const INVALID_NUMBER_LENGTH = '12345678901'; // 11 digits (invalid)

    public const INVALID_NUMBER_PATTERN = 'ABCDEFGHIJKL';

    public const VALID_NUMBER = [
        // Valid My Number (12 digits)
        '123456789018',
        '987654321098',
        
        // Valid Corporate Number (13 digits)
        '1234567890123',
        '5012345678909',
        '8012345678906',
    ];
    
    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '111111111111', // All same digits (My Number)
        '1111111111111', // All same digits (Corporate)
        '0123456789012', // Corporate number starting with 0
        '123456789010', // Invalid My Number checksum
        '1234567890124', // Invalid Corporate checksum
    ];
} 