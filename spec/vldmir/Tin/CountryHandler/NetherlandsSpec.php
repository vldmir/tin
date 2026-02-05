<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class NetherlandsSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '174559435';

    public const INVALID_NUMBER_LENGTH = '1745';

    public const INVALID_NUMBER_PATTERN = 'wwwwwwwww';

    public const VALID_NUMBER = '174559434';
}
