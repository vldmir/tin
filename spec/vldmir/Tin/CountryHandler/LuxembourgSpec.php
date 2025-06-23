<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class LuxembourgSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '1893120105733';

    public const INVALID_NUMBER_LENGTH = '18931201057321';

    public const INVALID_NUMBER_PATTERN = 'wwwwwwwwwwwww';

    public const VALID_NUMBER = '1893120105732';
}
