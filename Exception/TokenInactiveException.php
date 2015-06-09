<?php

namespace Innobyte\TokenBundle\Exception;

/**
 * Class TokenInactiveException
 * Thrown when a Token is trying to be consumed, but it is inactive
 *
 * @package Innobyte\TokenBundle\Exception
 *
 * @codeCoverageIgnore
 *
 * @author Sorin Dumitrescu <sorin.dumitrescu@innobyte.com>
 */
class TokenInactiveException extends \LogicException
{
}
