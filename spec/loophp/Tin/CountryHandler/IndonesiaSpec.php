<?php

declare(strict_types=1);

namespace spec\loophp\Tin\CountryHandler;

use tests\loophp\Tin\AbstractAlgorithmSpec;

class IndonesiaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '0012345678901234'; // Starts with 00

    public const INVALID_NUMBER_LENGTH = '012345678901234'; // Too short

    public const INVALID_NUMBER_PATTERN = 'ABCDEFGHIJKLMNOP';

    public const VALID_NUMBER = [
        // Valid NPWP numbers
        '01.234.567.8-901.234',
        '0123456789012345',
        '12.345.678.9-012.345',
        '1234567890123456',
        '31.000.000.1-000.000',
        '3100000010000000',
    ];
    
    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '0000000000000000', // All zeros
        '00.123.456.7-890.123', // Invalid tax office code (00)
    ];
} 