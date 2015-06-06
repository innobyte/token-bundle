<?php

/*
 * This file is part of the Fashion Days shop.
 *
 * (c) 2009-2015 Fashion Days Group AG <ml_it_dev@fashiondays.com>
 */

namespace Innobyte\TokenBundle\Exception;

/**
 * Class TokenInactiveException
 * Thrown when a Token is trying to be consumed, but it is inactive
 *
 * @package Innobyte\TokenBundle\Exception
 *
 * @author Sorin Dumitrescu <sorin.dumitrescu@innobyte.com>
 */
class TokenInactiveException extends \LogicException
{
}
