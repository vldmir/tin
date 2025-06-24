<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class NigeriaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '0123456789';

    public const INVALID_NUMBER_LENGTH = '123456789';

    public const INVALID_NUMBER_PATTERN = 'ABCDEFGHIJ';

    public const VALID_NUMBER = ['1234567890', '9876543210'];
}
