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

    public function it_can_be_constructed_from_a_slug()
    {
        $this
            ->beConstructedThrough('fromSlug', ['be1234567890']);

        $this
            ->shouldBeAnInstanceOf(TIN::class);
    }

    /**
     * Tests the validity check of TINs, ensuring that invalid TINs return false and valid TINs return true.
     */
    public function it_can_check_if_a_tin_is_valid_or_not()
    {
        $this
            ->beConstructedThrough('from', ['be', '1234567890']);

        $this::fromSlug('be123456789')
            ->isValid()
            ->shouldReturn(false);

        $this::fromSlug('be00012511119')
            ->isValid()
            ->shouldReturn(true);
    }

    /**
     * Tests that TIN types for a given country code are correctly returned.
     *
     * Verifies that the static method returns the expected array of TIN type definitions for Spain ('ES') and France ('FR').
     */
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

    /**
     * Tests that a TIN instance created from a Spanish slug returns the correct array of TIN types for Spain.
     */
    public function it_can_get_tin_types_from_instance()
    {
        $this::fromSlug('ES12345678Z')
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

    /**
     * Tests that the TIN type is correctly identified for various Spanish TIN formats (DNI, NIE, CIF).
     *
     * Verifies that the `identifyTinType` method returns the expected type information based on the TIN slug format for Spain.
     */
    public function it_can_identify_tin_type_for_spanish_tins()
    {
        // DNI example
        $this::fromSlug('ES12345678Z')
            ->identifyTinType()
            ->shouldReturn([
                'code' => 'DNI',
                'name' => 'Documento Nacional de Identidad',
                'description' => 'Spanish Natural Persons ID',
            ]);

        // NIE example (starts with X, Y, or Z)
        $this::fromSlug('ESX1234567L')
            ->identifyTinType()
            ->shouldReturn([
                'code' => 'NIE',
                'name' => 'Número de Identidad de Extranjero',
                'description' => 'Foreigners Identification Number',
            ]);

        // CIF example (starts with a letter A-W except vowels)
        $this::fromSlug('ESA12345674')
            ->identifyTinType()
            ->shouldReturn([
                'code' => 'CIF',
                'name' => 'Código de Identificación Fiscal',
                'description' => 'Tax Identification Code for Legal Entities',
            ]);
    }

    /**
     * Tests that a TINException is thrown when attempting to check a TIN with an unrecognized or invalid slug.
     */
    public function it_can_throw_an_exception_if_algorithm_is_not_found()
    {
        $this::fromSlug('foo1234')
            ->shouldThrow(TINException::class)
            ->during('check');

        $this::fromSlug('ww1234')
            ->shouldThrow(TINException::class)
            ->during('check');

        $this::fromSlug('ww')
            ->shouldThrow(TINException::class)
            ->during('check');

        $this::fromSlug('1234')
            ->shouldThrow(TINException::class)
            ->during('check');
    }

    /**
     * Verifies that the subject under test is an instance of the TIN class.
     */
    public function it_is_initializable()
    {
        $this->shouldHaveType(TIN::class);
    }

    /**
     * Verifies that requesting TIN types for an unsupported country code throws a TINException.
     */
    public function it_throws_exception_for_unsupported_country_tin_types()
    {
        $this
            ->shouldThrow(TINException::class)
            ->during('getTinTypesForCountry', ['ZZ']);
    }

    /**
     * Initializes the test subject using the 'fromSlug' constructor with a sample slug before each test.
     */
    public function let()
    {
        $this->beConstructedThrough('fromSlug', ['foo123']);
    }
}
