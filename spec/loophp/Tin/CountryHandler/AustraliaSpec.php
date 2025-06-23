<?php

declare(strict_types=1);

namespace spec\loophp\Tin\CountryHandler;

use tests\loophp\Tin\AbstractAlgorithmSpec;

class AustraliaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '11111111'; // Invalid TFN

    public const INVALID_NUMBER_LENGTH = '1234567'; // Too short

    public const INVALID_NUMBER_PATTERN = 'ABC DEF GHI JKL';

    public const VALID_NUMBER = [
        // Valid TFN numbers
        '865414088', // 9 digits
        '459599230', // 9 digits
        
        // Valid ABN numbers (with correct modulus 89 checksum)
        '53 004 085 616',
        '53004085616',
        '51 824 753 556',
        '51824753556',
        '11 000 001 243',
        '11000001243',
    ];
    
    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '00000000',     // All zeros TFN
        '12345678',     // Invalid TFN
        '00 000 000 000', // All zeros ABN
        '12 345 678 901', // Invalid ABN checksum
        '53 004 085 617', // Invalid ABN (wrong checksum)
    ];
} 