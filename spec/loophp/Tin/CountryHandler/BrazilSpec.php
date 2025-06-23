<?php

declare(strict_types=1);

namespace spec\loophp\Tin\CountryHandler;

use tests\loophp\Tin\AbstractAlgorithmSpec;

class BrazilSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '12345678901'; // Invalid CPF checksum

    public const INVALID_NUMBER_LENGTH = '123456789'; // Too short

    public const INVALID_NUMBER_PATTERN = 'ABC.DEF.GHI-JK';

    public const VALID_NUMBER = [
        // Valid CPF numbers with correct checksums
        '123.456.789-09',
        '12345678909',
        '111.444.777-35',
        '11144477735',
        
        // Valid CNPJ numbers with correct checksums
        '11.222.333/0001-81',
        '11222333000181',
        '11.444.777/0001-61',
        '11444777000161',
    ];
    
    // Additional test cases for invalid numbers
    public const INVALID_NUMBERS = [
        '111.111.111-11', // All same digits
        '000.000.000-00', // All zeros
        '123.456.789-00', // Invalid CPF checksum
        '11.222.333/0001-00', // Invalid CNPJ checksum
        '999.999.999-99', // All nines
    ];
} 