<?php

declare(strict_types=1);

namespace spec\loophp\Tin\CountryHandler;

use tests\loophp\Tin\AbstractAlgorithmSpec;

class MexicoSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = 'GODE561232GR8'; // Invalid date (32nd day)

    public const INVALID_NUMBER_LENGTH = 'GODE561231'; // Too short

    public const INVALID_NUMBER_PATTERN = '1234567890123';

    public const VALID_NUMBER = [
        // Valid personal RFC (13 characters)
        'GODE561231GR8',
        'MAPE800101ABC',
        'ROGE700315DE2',
        
        // Valid business RFC (12 characters)
        'ABC010203AB1',
        'XYZ991231Z99',
        'ABC800101ABC',
    ];
    
    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        'GODE561301GR8', // Invalid month (13)
        'GODE560001GR8', // Invalid day (00)
        'ABC001301AB1',  // Invalid month in business RFC
        'ABC000101AB1',  // Invalid year (00)
    ];
} 