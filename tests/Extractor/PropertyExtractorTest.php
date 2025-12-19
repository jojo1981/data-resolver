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

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Extractor\PropertyExtractor;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\MergeHandlerInterface;
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
use stdClass;
use function array_merge;

/**
 * @package tests\Jojo1981\DataResolver\Extractor
 */
final class PropertyExtractorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<NamingStrategyInterface> */
    private ObjectProphecy $namingStrategy;

    /** @var ObjectProphecy<PropertyHandlerInterface> */
    private ObjectProphecy $propertyHandler;

    /** @var ObjectProphecy<MergeHandlerInterface> */
    private ObjectProphecy $mergeHandler;

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
        $this->mergeHandler = $this->prophesize(MergeHandlerInterface::class);
        $this->context = $this->prophesize(Context::class);
        $this->context->setData(Argument::any())->shouldNotBeCalled();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->context->setPath(Argument::any())->shouldNotBeCalled();
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws ExtractorException
     */
    public function testExtractShouldThrowAnExceptionBecausePropertyHandlerDoesNotSupportThePropertyAndDataFromContext(
    ): void {
        $propertyName = 'property-name';
        /** @noinspection PhpParamsInspection */
        $this->mergeHandler->merge(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->context->getPath()->willReturn('my-path')->shouldBeCalledOnce();
        $this->propertyHandler->supports($propertyName, 'my-data')->willReturn(false)->shouldBeCalledOnce();
        /** @noinspection PhpStrictTypeCheckingInspection */
        /** @noinspection PhpParamsInspection */
        $this->propertyHandler->hasValueForPropertyName(
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldNotBeCalled();

        $this->expectExceptionObject(new ExtractorException('Could not extract data with `' . PropertyExtractor::class . '` for property: `property-name` at path: `my-path`'));

        $this->getPropertyExtractor($propertyName)->extract($this->context->reveal());
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws ExtractorException
     */
    public function testExtractShouldThrowAnExceptionBecausePropertyHandlerSupportThePropertyAndDataFromContextButHasNoValueForThePropertyName(
    ): void {
        $propertyName = 'property-name';
        /** @noinspection PhpParamsInspection */
        $this->mergeHandler->merge(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->context->getPath()->willReturn('my-path')->shouldBeCalledOnce();
        $this->propertyHandler->supports($propertyName, 'my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler->hasValueForPropertyName(
            $this->namingStrategy,
            $propertyName,
            'my-data'
        )->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(new ExtractorException('Could not extract data with `' . PropertyExtractor::class . '` for property: `property-name` at path: `my-path`'));

        $this->getPropertyExtractor($propertyName)->extract($this->context->reveal());
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     * @throws HandlerException
     */
    public function testExtractShouldReturnTheResultFromThePropertyHandlerGetValueForPropertyNameMethod(): void
    {
        $propertyName = 'the-prop';
        /** @noinspection PhpParamsInspection */
        $this->mergeHandler->merge(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->context->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->context->getPath()->shouldNotBeCalled();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->context->pushPathPart($propertyName)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->context->popPathPart()->shouldBeCalledOnce();
        $this->propertyHandler->supports($propertyName, 'my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler->hasValueForPropertyName(
            $this->namingStrategy,
            $propertyName,
            'my-data'
        )->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler->getValueForPropertyName(
            $this->namingStrategy,
            $propertyName,
            'my-data'
        )->willReturn('returned-value')->shouldBeCalledOnce();

        self::assertEquals(
            'returned-value',
            $this->getPropertyExtractor($propertyName)->extract($this->context->reveal())
        );
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     */
    public function testExtractWithMultiplePropertiesShouldReturnTheResultFromTheMergeHandler(): void
    {
        $propertyNames = ['prop1', 'prop2'];
        $resolvedValues = ['value1', 'value2'];

        /** @noinspection PhpUndefinedMethodInspection */
        $this->context->popPathPart()->shouldBeCalledTimes(2);
        $this->context->getData()->willReturn('my-data')->shouldBeCalledTimes(4);

        $this->context->getPath()->shouldNotBeCalled();
        foreach ($propertyNames as $index => $propertyName) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->context->pushPathPart($propertyName)->shouldBeCalledOnce();
            $this->propertyHandler->supports($propertyName, 'my-data')->willReturn(true)->shouldBeCalledOnce();
            $this->propertyHandler->hasValueForPropertyName(
                $this->namingStrategy,
                $propertyName,
                'my-data'
            )->willReturn(true)->shouldBeCalledOnce();
            $this->propertyHandler->getValueForPropertyName(
                $this->namingStrategy,
                $propertyName,
                'my-data'
            )->willReturn($resolvedValues[$index])->shouldBeCalledOnce();
        }

        $result = new stdClass();
        $this->mergeHandler->merge(
            $this->context,
            ['prop1' => 'value1', 'prop2' => 'value2']
        )->shouldBeCalled()->willReturn($result);

        self::assertSame($result, $this->getPropertyExtractor(...$propertyNames)->extract($this->context->reveal()));
    }

    /**
     * @param string $propertyName
     * @param string ...$propertyNames
     * @return PropertyExtractor
     * @throws ObjectProphecyException
     */
    private function getPropertyExtractor(string $propertyName, ...$propertyNames): PropertyExtractor
    {
        return new PropertyExtractor(
            $this->namingStrategy->reveal(),
            $this->propertyHandler->reveal(),
            $this->mergeHandler->reveal(),
            array_merge($propertyNames, [$propertyName])
        );
    }
}
