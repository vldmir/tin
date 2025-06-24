<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class FranceSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_LENGTH = '30232176000531';

    public const INVALID_NUMBER_PATTERN = ['9111111111111'];

    public const INVALID_NUMBER_SYNTAX = ['1111111111111'];

    public const VALID_NUMBER = '3023217600053';
}
