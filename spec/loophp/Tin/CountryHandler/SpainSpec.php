<?php

declare(strict_types=1);

namespace spec\loophp\Tin\CountryHandler;

use tests\loophp\Tin\AbstractAlgorithmSpec;

class SpainSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = ['X1234567Z', 'P2009300B', 'K0867756J'];

    public const INVALID_NUMBER_LENGTH = '542372254545445A';

    public const INVALID_NUMBER_PATTERN = ['wwwwwwwww', 'K0867756N'];

    public const VALID_NUMBER = ['54237A', 'X1234567L', 'Y1234567X', 'Z1234567R', 'M2812345C', 'B05327986', 'P2009300A', 'K0867756I'];

    public function it_can_identify_dni_type()
    {
        $this->beConstructedWith();
        
        $result = $this->identifyTinType('12345678Z');
        $result->shouldReturn([
            'code' => 'DNI',
            'name' => 'Documento Nacional de Identidad',
            'description' => 'Spanish Natural Persons ID',
        ]);
    }

    public function it_can_identify_nie_type()
    {
        $this->beConstructedWith();
        
        $result = $this->identifyTinType('X1234567L');
        $result->shouldReturn([
            'code' => 'NIE',
            'name' => 'Número de Identidad de Extranjero',
            'description' => 'Foreigners Identification Number',
        ]);
    }

    public function it_can_identify_cif_type()
    {
        $this->beConstructedWith();
        
        $result = $this->identifyTinType('A12345674');
        $result->shouldReturn([
            'code' => 'CIF',
            'name' => 'Código de Identificación Fiscal',
            'description' => 'Tax Identification Code for Legal Entities',
        ]);
    }

    public function it_returns_all_tin_types()
    {
        $this->beConstructedWith();
        
        $this->getTinTypes()->shouldReturn([
            1 => [
                'code' => 'DNI',
                'name' => 'Documento Nacional de Identidad',
                'description' => 'Spanish Natural Persons ID',
            ],
            2 => [
                'code' => 'NIE',
                'name' => 'Número de Identidad de Extranjero',
                'description' => 'Foreigners Identification Number',
            ],
            3 => [
                'code' => 'CIF',
                'name' => 'Código de Identificación Fiscal',
                'description' => 'Tax Identification Code for Legal Entities',
            ],
        ]);
    }
}
