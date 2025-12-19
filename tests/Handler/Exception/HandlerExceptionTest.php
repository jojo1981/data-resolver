<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
declare(strict_types=1);

namespace tests\Jojo1981\DataResolver\Handler\Exception;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * @package tests\Jojo1981\DataResolver\Handler\Exception
 */
final class HandlerExceptionTest extends TestCase
{
    /**
     * @param string $exceptionMessage
     * @param string $className
     * @param string $invokedMethodName
     * @param string $assertMethodName
     * @param string|null $extraMessage
     * @return void
     * @throws ExpectationFailedException
     */
    #[DataProvider("getIllegalMethodInvocationTestData")]
    public function testIllegalMethodInvocationShouldReturnHandlerException(
        string $exceptionMessage,
        string $className,
        string $invokedMethodName,
        string $assertMethodName,
        ?string $extraMessage = null
    ): void {
        self::assertEquals(
            new HandlerException($exceptionMessage),
            HandlerException::IllegalMethodInvocation($className, $invokedMethodName, $assertMethodName, $extraMessage)
        );
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testCouldNotGetReflectionShouldReturnHandlerException(): void
    {
        $reflectionException = new ReflectionException();
        $expectedResult = new HandlerException('Can not get reflection', 0, $reflectionException);
        $actualResult = HandlerException::couldNotGetReflection($reflectionException);

        self::assertEquals($expectedResult, $actualResult);
        self::assertSame($actualResult->getPrevious(), $reflectionException);
    }

    /**
     * @return array[]
     */
    public static function getIllegalMethodInvocationTestData(): array
    {
        return [
            [
                'The `my-class1`. Illegal invocation of method `invoked-method1`. You should invoke the' .
                ' `assert-method1` method first!',
                'my-class1',
                'invoked-method1',
                'assert-method1'
            ],
            [
                'The `my-class2` extra message. Illegal invocation of method `invoked-method2`. You should invoke the' .
                ' `assert-method2` method first!',
                'my-class2',
                'invoked-method2',
                'assert-method2',
                'extra message'
            ]
        ];
    }
}
