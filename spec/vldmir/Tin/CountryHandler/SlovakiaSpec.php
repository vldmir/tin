<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class SlovakiaSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_LENGTH = '77111674201';

    public const INVALID_SYNTAX = '2812030541';

    public const VALID_NUMBER = '7711167420';

    public const VALID_NUMBER2 = '281203054';

    public const VALID_NUMBER3 = '2822030541';
}
