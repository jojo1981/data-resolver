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

namespace tests\Jojo1981\DataResolver\Extractor;

use Exception;
use Jojo1981\DataResolver\Extractor\HasPropertyExtractor;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\InvalidArgumentException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @package tests\Jojo1981\DataResolver\Extractor
 */
final class HasPropertyExtractorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<NamingStrategyInterface> */
    private ObjectProphecy $namingStrategy;

    /** @var ObjectProphecy<PropertyHandlerInterface> */
    private ObjectProphecy $propertyHandler;

    /** @var ObjectProphecy<Context> */
    private ObjectProphecy $context;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws InvalidArgumentException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->namingStrategy = $this->prophesize(NamingStrategyInterface::class);
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->namingStrategy->getPropertyNames(Argument::any())->shouldNotBeCalled();
        $this->propertyHandler = $this->prophesize(PropertyHandlerInterface::class);
        $this->context = $this->prophesize(Context::class);
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function testExtractShouldReturnFalseWhenPropertyHandlerDoesNotSupport(): void
    {
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->propertyHandler->supports('propertyName', 'my-data')->willReturn(false)->shouldBeCalledOnce();
        self::assertFalse($this->getHasPropertyExtractor()->extract($this->context->reveal()));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     */
    public function testExtractShouldReturnFalseWhenPropertyHandlerHasValueForPropertyNameThrowsAnException(): void
    {
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->propertyHandler->supports('propertyName', 'my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler->hasValueForPropertyName($this->namingStrategy, 'propertyName', 'my-data')
            ->willThrow(Exception::class)
            ->shouldBeCalledOnce();

        self::assertFalse($this->getHasPropertyExtractor()->extract($this->context->reveal()));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     */
    public function testExtractShouldReturnTrueWhenPropertyHandlerHasValueForPropertyNameReturnsTrue(): void
    {
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->propertyHandler->supports('propertyName', 'my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler->hasValueForPropertyName($this->namingStrategy, 'propertyName', 'my-data')
            ->willReturn(true)
            ->shouldBeCalledOnce();

        self::assertTrue($this->getHasPropertyExtractor()->extract($this->context->reveal()));
    }

    /**
     * @return HasPropertyExtractor
     * @throws ObjectProphecyException
     */
    private function getHasPropertyExtractor(): HasPropertyExtractor
    {
        return new HasPropertyExtractor(
            $this->propertyHandler->reveal(),
            $this->namingStrategy->reveal(),
            'propertyName'
        );
    }
}
