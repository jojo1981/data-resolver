<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Handler\Exception;

use Jojo1981\DataResolver\Exception\ResolverException;
use ReflectionException;

/**
 * Base exception class for when exceptions occur during the handling process
 *
 * @package Jojo1981\DataResolver\Handler\Exception
 */
class HandlerException extends ResolverException
{
    /**
     * @param string $className
     * @param string $invokedMethodName
     * @param string $assertMethodName
     * @param string|null $extraMessage
     * @return HandlerException
     */
    public static function IllegalMethodInvocation(
        string $className,
        string $invokedMethodName,
        string $assertMethodName,
        ?string $extraMessage = null
    ): HandlerException {
        return new static(\sprintf(
            'The `%s`%s. Illegal invocation of method `%s`. You should invoke the `%s` method first!',
            $className,
            null !== $extraMessage ? ' ' . $extraMessage : '',
            $invokedMethodName,
            $assertMethodName
        ));
    }

    /**
     * @param ReflectionException $reflectionException
     * @return HandlerException
     */
    public static function couldNotGetReflection(ReflectionException $reflectionException): HandlerException
    {
        return new static('Can not get reflection', 0, $reflectionException);
    }
}
