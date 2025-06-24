<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class AustriaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '931736582';

    public const INVALID_NUMBER_LENGTH = '9317365815';

    public const INVALID_NUMBER_PATTERN = ['1w1w1w1w1'];

    public const VALID_NUMBER = '931736581';
}
