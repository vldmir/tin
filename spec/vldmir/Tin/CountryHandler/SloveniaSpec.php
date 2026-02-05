<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class SloveniaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '15012558';

    public const INVALID_NUMBER_LENGTH = '150125571';

    public const INVALID_NUMBER_PATTERN = 'wwwwwwww';

    public const VALID_NUMBER = '15012557';
}
