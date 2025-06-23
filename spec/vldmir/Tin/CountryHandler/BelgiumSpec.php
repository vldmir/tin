<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

class BelgiumSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = ['00012511120'];

    public const INVALID_NUMBER_DATE = '81023011101';

    public const INVALID_NUMBER_LENGTH = '0001251112020';

    public const INVALID_NUMBER_PATTERN = ['wwwwwwwwwww'];

    public const INVALID_NUMBER_SYNTAX = ['00022911101'];

    public const VALID_NUMBER = ['00012511119', '01062624339'];
}
