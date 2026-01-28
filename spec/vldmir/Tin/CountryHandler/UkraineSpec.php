<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use PhpSpec\ObjectBehavior;
use vldmir\Tin\CountryHandler\Ukraine;

class UkraineSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Ukraine::class);
    }

    public function it_should_get_tin_types(): void
    {
        $this->getTinTypes()->shouldReturn([
            1 => [
                'code' => 'INDIVIDUAL_TAX_NUMBER',
                'name' => 'Individual Tax Number',
                'description' => 'Individual taxpayer identification number',
            ],
        ]);
    }

    public function it_should_have_correct_country_code(): void
    {
        $this->getCountryCode()->shouldReturn('UA');
    }

    public function it_should_have_correct_length(): void
    {
        $this->getLength()->shouldReturn(10);
    }

    public function it_should_have_correct_mask(): void
    {
        $this->getInputMask()->shouldReturn('9999999999');
    }

    public function it_should_have_correct_pattern(): void
    {
        $this->getPattern()->shouldReturn('^\d{10}$');
    }

    public function it_should_have_correct_placeholder(): void
    {
        $this->getPlaceholder()->shouldReturn('1234567890');
    }

    public function it_should_identify_tin_type(): void
    {
        $this->identifyTinType('5632582743')->shouldReturn([
            'code' => 'INDIVIDUAL_TAX_NUMBER',
            'name' => 'Individual Tax Number',
            'description' => 'Individual taxpayer identification number',
        ]);

        $this->identifyTinType('5632582744')->shouldReturn(null); // Invalid checksum
        $this->identifyTinType('123456789')->shouldReturn(null); // Too short
    }

    public function it_should_normalize_tin(): void
    {
        $this->normalizeTin('563-258-2743')->shouldReturn('5632582743');
        $this->normalizeTin('563 258 2743')->shouldReturn('5632582743');
        $this->normalizeTin('563.258.2743')->shouldReturn('5632582743');
        $this->normalizeTin('5632582743')->shouldReturn('5632582743');
    }

    public function it_should_not_support_other_country_codes(): void
    {
        $this->supports('US')->shouldReturn(false);
        $this->supports('DE')->shouldReturn(false);
        $this->supports('FR')->shouldReturn(false);
    }

    public function it_should_support_ukraine_country_code(): void
    {
        $this->supports('UA')->shouldReturn(true);
        $this->supports('ua')->shouldReturn(true);
        $this->supports('Ua')->shouldReturn(true);
    }

    public function it_should_validate_checksum(): void
    {
        // Valid TINs with correct checksum
        $this->validate('5632582743')->shouldReturn(true);
        $this->validate('2935277368')->shouldReturn(true);
        $this->validate('5555555555')->shouldReturn(true); // This one happens to be valid
    }

    public function it_should_throw_for_invalid_checksum(): void
    {
        // Invalid TINs with wrong checksum
        $this->shouldThrow(\vldmir\Tin\Exception\TINException::class)->during('validate', ['5632582744']); // Wrong last digit
    }

    public function it_should_validate_ukraine_tin(): void
    {
        // Valid TINs with correct checksum
        $this->validate('5632582743')->shouldReturn(true);
        $this->validate('2935277368')->shouldReturn(true);
        $this->validate('5566567954')->shouldReturn(true);
    }

    public function it_should_throw_for_invalid_format(): void
    {
        // Invalid TINs - all same digits
        $this->shouldThrow(\vldmir\Tin\Exception\TINException::class)->during('validate', ['1111111111']);
    }

    public function it_should_throw_for_all_zeros(): void
    {
        $this->shouldThrow(\vldmir\Tin\Exception\TINException::class)->during('validate', ['0000000000']);
    }

    public function it_should_throw_for_short_tin(): void
    {
        $this->shouldThrow(\vldmir\Tin\Exception\TINException::class)->during('validate', ['123456789']);
    }

    public function it_should_throw_for_long_tin(): void
    {
        $this->shouldThrow(\vldmir\Tin\Exception\TINException::class)->during('validate', ['12345678901']);
    }
}
