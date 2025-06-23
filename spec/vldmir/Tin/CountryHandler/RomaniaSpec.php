<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class RomaniaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_DATE = '8001611234567';

    public const INVALID_NUMBER_LENGTH = '80010112345671';

    public const VALID_NUMBER = '8001011234567';
}
