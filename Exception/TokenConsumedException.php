<?php

namespace Innobyte\TokenBundle\Exception;

/**
 * Class TokenConsumedException
 * Thrown when a Token is trying to be consumed, but it is already consumed
 *
 * @package Innobyte\TokenBundle\Exception
 *
 * @codeCoverageIgnore
 *
 * @author Sorin Dumitrescu <sorin.dumitrescu@innobyte.com>
 */
class TokenConsumedException extends TokenException
{
}
