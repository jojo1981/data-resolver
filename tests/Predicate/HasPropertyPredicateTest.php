<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use Jojo1981\DataResolver\Predicate\HasPropertyPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
class HasPropertyPredicateTest extends TestCase
{
    /** @var ObjectProphecy|NamingStrategyInterface */
    private $namingStrategy;

    /** @var ObjectProphecy|PropertyHandlerInterface */
    private $propertyHandler;

    /** @var ObjectProphecy|Context */
    private $context;

    /**
     * @throws DoubleException
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @return void
     */
    protected function setUp(): void
    {
        $this->namingStrategy = $this->prophesize(NamingStrategyInterface::class);
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();
        $this->namingStrategy->getPropertyNames(Argument::any())->shouldNotBeCalled();
        $this->propertyHandler = $this->prophesize(PropertyHandlerInterface::class);
        $this->context = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @return void
     */
    public function matchShouldReturnFalseWhenPropertyHandlerDoesNotSupport(): void
    {
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->propertyHandler->supports('propertyName', 'my-data')->willReturn(false)->shouldBeCalledOnce();
        $this->assertFalse($this->getHasPropertyPredicate('propertyName')->match($this->context->reveal()));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnFalseWhenPropertyHandlerHasValueForPropertyNameThrowsAnException(): void
    {
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->propertyHandler->supports('propertyName', 'my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler->hasValueForPropertyName($this->namingStrategy, 'propertyName', 'my-data')
            ->willThrow(\Exception::class)
            ->shouldBeCalledOnce();

        $this->assertFalse($this->getHasPropertyPredicate('propertyName')->match($this->context->reveal()));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnTrueWhenPropertyHandlerHasValueForPropertyNameReturnsTrue(): void
    {
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->propertyHandler->supports('propertyName', 'my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler->hasValueForPropertyName($this->namingStrategy, 'propertyName', 'my-data')
            ->willReturn(true)
            ->shouldBeCalledOnce();

        $this->assertTrue($this->getHasPropertyPredicate('propertyName')->match($this->context->reveal()));
    }

    /**
     * @param string $propertyName
     * @throws ObjectProphecyException
     * @return HasPropertyPredicate
     */
    private function getHasPropertyPredicate(string $propertyName): HasPropertyPredicate
    {
        return new HasPropertyPredicate(
            $this->propertyHandler->reveal(),
            $this->namingStrategy->reveal(),
            $propertyName
        );
    }
}