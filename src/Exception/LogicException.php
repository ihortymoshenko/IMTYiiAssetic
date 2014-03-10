<?php

/*
 * This file is part of the IMTYiiAssetic package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\YiiAssetic\Exception;

/**
 * This class represents the exception that is thrown if an error in the program
 * logic occurs. This kind of exceptions should directly lead to a fix in your
 * code
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class LogicException extends \LogicException implements ExceptionInterface
{
}
