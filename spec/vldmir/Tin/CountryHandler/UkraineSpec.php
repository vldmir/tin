<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use PhpSpec\ObjectBehavior;
use vldmir\Tin\CountryHandler\Ukraine;

class UkraineSpec extends ObjectBehavior
{
    /**
     * Verifies that the Ukraine class can be instantiated.
     */
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Ukraine::class);
    }

    /**
     * Tests that the getTinTypes() method returns the expected array of Ukrainian TIN types.
     */
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

    /**
     * Verifies that the country code returned is 'UA' for Ukraine.
     */
    public function it_should_have_correct_country_code(): void
    {
        $this->getCountryCode()->shouldReturn('UA');
    }

    /**
     * Verifies that the expected TIN length for Ukraine is 10 digits.
     */
    public function it_should_have_correct_length(): void
    {
        $this->getLength()->shouldReturn(10);
    }

    /**
     * Verifies that the input mask for Ukrainian TINs is '9999999999'.
     */
    public function it_should_have_correct_mask(): void
    {
        $this->getInputMask()->shouldReturn('9999999999');
    }

    /**
     * Verifies that the TIN pattern for Ukraine matches exactly 10 digits.
     */
    public function it_should_have_correct_pattern(): void
    {
        $this->getPattern()->shouldReturn('^\d{10}$');
    }

    /**
     * Verifies that the placeholder for a Ukrainian TIN is '1234567890'.
     */
    public function it_should_have_correct_placeholder(): void
    {
        $this->getPlaceholder()->shouldReturn('1234567890');
    }

    /**
     * Tests that the TIN type is correctly identified for valid Ukrainian TINs and returns null for invalid checksums or incorrect lengths.
     */
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

    /**
     * Ensures that the TIN normalization removes common separators and returns a 10-digit string.
     */
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

    /**
     * Tests that the validate() method correctly accepts TINs with valid checksums and throws a TINException for TINs with invalid checksums.
     */
    public function it_should_validate_checksum(): void
    {
        // Valid TINs with correct checksum
        $this->validate('5632582743')->shouldReturn(true);
        $this->validate('2935277368')->shouldReturn(true);
        $this->validate('5555555555')->shouldReturn(true); // This one happens to be valid

        // Invalid TINs with wrong checksum
        $this->validate('5632582744')->shouldThrow(\vldmir\Tin\Exception\TINException::class); // Wrong last digit
        $this->validate('2935277369')->shouldThrow(\vldmir\Tin\Exception\TINException::class); // Wrong last digit
        $this->validate('5555555556')->shouldThrow(\vldmir\Tin\Exception\TINException::class); // Wrong last digit
    }

    /**
     * Tests the validation of Ukrainian TINs, ensuring correct TINs are accepted and invalid formats or checksums throw a TINException.
     */
    public function it_should_validate_ukraine_tin(): void
    {
        // Valid TINs with correct checksum
        $this->validate('5632582743')->shouldReturn(true);
        $this->validate('2935277368')->shouldReturn(true);
        $this->validate('5566567954')->shouldReturn(true);

        // Invalid TINs - wrong format/length
        $this->validate('1111111111')->shouldThrow(\vldmir\Tin\Exception\TINException::class); // All same digits
        $this->validate('0000000000')->shouldThrow(\vldmir\Tin\Exception\TINException::class); // All zeros
        $this->validate('123456789')->shouldThrow(\vldmir\Tin\Exception\TINException::class); // Too short
        $this->validate('12345678901')->shouldThrow(\vldmir\Tin\Exception\TINException::class); // Too long
        $this->validate('123456789a')->shouldThrow(\vldmir\Tin\Exception\TINException::class); // Contains letter
        $this->validate('123456789-')->shouldThrow(\vldmir\Tin\Exception\TINException::class); // Contains dash
    }
}
