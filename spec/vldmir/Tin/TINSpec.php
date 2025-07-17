<?php

declare(strict_types=1);

namespace spec\vldmir\Tin;

use PhpSpec\ObjectBehavior;
use vldmir\Tin\Exception\TINException;
use vldmir\Tin\TIN;

class TINSpec extends ObjectBehavior
{
    public function it_can_be_constructed_from_a_countrycode_and_tin()
    {
        $this
            ->beConstructedThrough('from', ['be', '1234567890']);

        $this
            ->shouldBeAnInstanceOf(TIN::class);
    }

    public function it_can_be_constructed_from_country_and_tin()
    {
        $this
            ->beConstructedThrough('from', ['BE', '1234567890']);

        $this
            ->shouldBeAnInstanceOf(TIN::class);
    }

    public function it_can_check_if_a_tin_is_valid_or_not()
    {
        $this
            ->beConstructedThrough('from', ['be', '1234567890']);

        $this::from('BE', '123456789')
            ->isValid()
            ->shouldReturn(false);

        $this::from('BE', '00012511119')
            ->isValid()
            ->shouldReturn(true);
    }

    public function it_can_get_tin_types_for_a_country()
    {
        $this::getTinTypesForCountry('ES')
            ->shouldReturn([
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

        $this::getTinTypesForCountry('FR')
            ->shouldReturn([
                1 => [
                    'code' => 'TIN',
                    'name' => 'Tax Identification Number',
                    'description' => 'Standard tax identification number for FR',
                ],
            ]);
    }

    public function it_can_get_tin_types_from_instance()
    {
        $this::from('ES', '12345678Z')
            ->getTinTypes()
            ->shouldReturn([
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

    public function it_can_identify_tin_type_for_spanish_tins()
    {
        // DNI example
        $this::from('ES', '12345678Z')
            ->identifyTinType()
            ->shouldReturn([
                'code' => 'DNI',
                'name' => 'Documento Nacional de Identidad',
                'description' => 'Spanish Natural Persons ID',
            ]);

        // NIE example (starts with X, Y, or Z)
        $this::from('ES', 'X1234567L')
            ->identifyTinType()
            ->shouldReturn([
                'code' => 'NIE',
                'name' => 'Número de Identidad de Extranjero',
                'description' => 'Foreigners Identification Number',
            ]);

        // CIF example (starts with a letter A-W except vowels)
        $this::from('ES', 'A12345674')
            ->identifyTinType()
            ->shouldReturn([
                'code' => 'CIF',
                'name' => 'Código de Identificación Fiscal',
                'description' => 'Tax Identification Code for Legal Entities',
            ]);
    }

    public function it_can_throw_an_exception_if_algorithm_is_not_found()
    {
        $this::from('FO', '1234')
            ->shouldThrow(TINException::class)
            ->during('check');

        $this::from('WW', '1234')
            ->shouldThrow(TINException::class)
            ->during('check');

        $this::from('ZZ', '')
            ->shouldThrow(TINException::class)
            ->during('check');

        $this::from('XX', '1234')
            ->shouldThrow(TINException::class)
            ->during('check');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TIN::class);
    }

    public function it_throws_exception_for_unsupported_country_tin_types()
    {
        $this
            ->shouldThrow(TINException::class)
            ->during('getTinTypesForCountry', ['ZZ']);
    }

    public function let()
    {
        $this->beConstructedThrough('from', ['FO', '123']);
    }
}
