<?php

declare(strict_types=1);

namespace spec\vldmir\Tin\CountryHandler;

use tests\vldmir\Tin\AbstractAlgorithmSpec;

/**
 * Finnish HETU validation spec.
 * Numbers are tested in normalized form (without century separator).
 */
class FinlandSpec extends AbstractAlgorithmSpec
{
    public const INVALID_NUMBER_CHECK = '131052308Z';

    public const INVALID_NUMBER_DATE = '191952308T';

    public const INVALID_NUMBER_LENGTH = '13105230T';

    public const INVALID_NUMBER_PATTERN = '9910523081';

    /**
     * Valid HETUs include both letter and digit check characters.
     * - 131052308T: check char is letter (T), remainder 25
     * - 1310520040: check char is digit (0), remainder 0
     */
    public const VALID_NUMBER = ['131052308T', '1310520040'];
}
