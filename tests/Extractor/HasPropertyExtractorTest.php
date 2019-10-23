<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Extractor;

use Jojo1981\DataResolver\Extractor\HasPropertyExtractor;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
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
 * @package tests\Jojo1981\DataResolver\Extractor
 */
class HasPropertyExtractorTest extends TestCase
{
    /** @var ObjectProphecy|NamingStrategyInterface */
    private $namingStrategy;

    /** @var ObjectProphecy|PropertyHandlerInterface */
    private $propertyHandler;

    /** @var ObjectProphecy|Context */
    private $context;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
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
     * @return void
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function extractShouldReturnFalseWhenPropertyHandlerDoesNotSupport(): void
    {
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->propertyHandler->supports('propertyName', 'my-data')->willReturn(false)->shouldBeCalledOnce();
        $this->assertFalse($this->getHasPropertyExtractor('propertyName')->extract($this->context->reveal()));
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function extractShouldReturnFalseWhenPropertyHandlerHasValueForPropertyNameThrowsAnException(): void
    {
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->propertyHandler->supports('propertyName', 'my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler->hasValueForPropertyName($this->namingStrategy, 'propertyName', 'my-data')
            ->willThrow(\Exception::class)
            ->shouldBeCalledOnce();

        $this->assertFalse($this->getHasPropertyExtractor('propertyName')->extract($this->context->reveal()));
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function extractShouldReturnTrueWhenPropertyHandlerHasValueForPropertyNameReturnsTrue(): void
    {
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->propertyHandler->supports('propertyName', 'my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler->hasValueForPropertyName($this->namingStrategy, 'propertyName', 'my-data')
            ->willReturn(true)
            ->shouldBeCalledOnce();

        $this->assertTrue($this->getHasPropertyExtractor('propertyName')->extract($this->context->reveal()));
    }

    /**
     * @param string $propertyName
     * @return HasPropertyExtractor
     * @throws ObjectProphecyException
     */
    private function getHasPropertyExtractor(string $propertyName): HasPropertyExtractor
    {
        return new HasPropertyExtractor(
            $this->propertyHandler->reveal(),
            $this->namingStrategy->reveal(),
            $propertyName
        );
    }
}