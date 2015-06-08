<?php

namespace Innobyte\TokenBundle\Exception;

/**
 * Class TokenExpiredException
 * Thrown when a Token is trying to be consumed, but it is expired
 *
 * @package Innobyte\TokenBundle\Exception
 *
 * @author Sorin Dumitrescu <sorin.dumitrescu@innobyte.com>
 */
class TokenExpiredException extends \LogicException
{
}
