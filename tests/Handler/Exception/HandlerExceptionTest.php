<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Handler\Exception;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Handler\Exception
 */
class HandlerExceptionTest extends TestCase
{
    /**
     * @test
     * @dataProvider getIllegalMethodInvocationTestData
     *
     * @param string $exceptionMessage
     * @param string $className
     * @param string $invokedMethodName
     * @param string $assertMethodName
     * @param string|null $extraMessage
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @return void
     */
    public function IllegalMethodInvocationShouldReturnHandlerException(
        string $exceptionMessage,
        string $className,
        string $invokedMethodName,
        string $assertMethodName,
        ?string $extraMessage = null
    ): void
    {
        $this->assertEquals(
            new HandlerException($exceptionMessage),
            HandlerException::IllegalMethodInvocation($className, $invokedMethodName, $assertMethodName, $extraMessage)
        );
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function couldNotGetReflectionShouldReturnHandlerException(): void
    {
        $reflectionException = new \ReflectionException();
        $expectedResult = new HandlerException('Can not get reflection', 0, $reflectionException);
        $actualResult = HandlerException::couldNotGetReflection($reflectionException);

        $this->assertEquals($expectedResult, $actualResult);
        $this->assertSame($actualResult->getPrevious(), $reflectionException);
    }

    /**
     * @return array[]
     */
    public function getIllegalMethodInvocationTestData(): array
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