<?php

declare(strict_types=1);

namespace spec\loophp\Tin\CountryHandler;

use tests\loophp\Tin\AbstractAlgorithmSpec;

class UnitedStatesSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '123456789';

    public const INVALID_NUMBER_LENGTH = '12345678901';

    public const INVALID_NUMBER_PATTERN = 'ABC-DE-FGHI';

    public const VALID_NUMBER = [
        // Valid SSN formats
        '123-45-6789',
        '123456789',
        '234-56-7890',
        
        // Valid ITIN formats (starts with 9, fourth-fifth digits in valid ranges)
        '950-70-1234',
        '965-88-5678',
        '990-92-9012',
        '994-99-3456',
        
        // Valid EIN formats
        '12-3456789',
        '52-1234567',
        '91-9876543',
        '98-7654321',
    ];
    
    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '000-00-0000', // All zeros
        '666-12-3456', // Invalid area code 666
        '123-00-4567', // Zero group
        '456-78-0000', // Zero serial
        '900-12-3456', // Invalid SSN starting with 900 (not valid ITIN range)
        '950-40-1234', // ITIN with invalid fourth-fifth digits
        '07-1234567',  // Invalid EIN prefix
        '89-1234567',  // Invalid EIN prefix
    ];
} 