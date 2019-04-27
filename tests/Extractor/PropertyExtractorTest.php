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

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Extractor\PropertyExtractor;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Extractor
 */
class PropertyExtractorTest extends TestCase
{
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
        $this->propertyHandler = $this->prophesize(PropertyHandlerInterface::class);
        $this->context = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @return void
     */
    public function extractShouldThrowAnExceptionBecausePropertyHandlerDoesNotSupportThePropertyAndDataFromContext(): void
    {
        $propertyName = 'property-name';
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->context->getPath()->willReturn('my-path')->shouldBeCalledOnce();
        $this->propertyHandler->supports($propertyName, 'my-data')->willReturn(false)->shouldBeCalled();

        $this->expectExceptionObject(new ExtractorException('Could not extract data with `' . PropertyExtractor::class . '` for property: `property-name` at path: `my-path`'));

        $this->getPropertyExtractor($propertyName)->extract($this->context->reveal());
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @return void
     */
    public function extractShouldThrowAnExceptionBecausePropertyHandlerSupportThePropertyAndDataFromContextButHasNoValueForThePropertyName(): void
    {
        $propertyName = 'property-name';
        $this->context->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->context->getPath()->willReturn('my-path')->shouldBeCalledOnce();
        $this->propertyHandler->supports($propertyName, 'my-data')->willReturn(true)->shouldBeCalled();
        $this->propertyHandler->hasValueForPropertyName($propertyName, 'my-data')->willReturn(false)->shouldBeCalled();

        $this->expectExceptionObject(new ExtractorException('Could not extract data with `' . PropertyExtractor::class . '` for property: `property-name` at path: `my-path`'));

        $this->getPropertyExtractor($propertyName)->extract($this->context->reveal());
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ExtractorException
     * @return void
     */
    public function extractShouldReturnTheResultFromThePropertyHandlerGetValueForPropertyNameMethod(): void
    {
        $propertyName = 'the-prop';
        $this->context->getData()->willReturn('my-data')->shouldBeCalledTimes(3);
        $this->context->getPath()->shouldNotBeCalled();
        $this->context->pushPathPart($propertyName)->shouldBeCalledOnce();
        $this->propertyHandler->supports($propertyName, 'my-data')->willReturn(true)->shouldBeCalled();
        $this->propertyHandler->hasValueForPropertyName($propertyName, 'my-data')->willReturn(true)->shouldBeCalled();
        $this->propertyHandler->getValueForPropertyName($propertyName, 'my-data')->willReturn('returned-value')->shouldBeCalled();

        $this->assertEquals('returned-value', $this->getPropertyExtractor($propertyName)->extract($this->context->reveal()));
    }

    /**
     * @param string $propertyName
     * @throws ObjectProphecyException
     * @return PropertyExtractor
     */
    private function getPropertyExtractor(string $propertyName): PropertyExtractor
    {
        return new PropertyExtractor($this->propertyHandler->reveal(), $propertyName);
    }
}