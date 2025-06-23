<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class DenmarkSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = ['0101111114', '0101477000'];

    public const INVALID_NUMBER_DATE = '3119999999';

    public const INVALID_NUMBER_LENGTH = '01011111132';

    public const INVALID_NUMBER_PATTERN = '9101111113';

    public const VALID_NUMBER = ['0101111113', '0101601111'];
}
