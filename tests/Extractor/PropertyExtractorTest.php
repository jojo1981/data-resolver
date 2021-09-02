<?php declare(strict_types=1);
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
use Jojo1981\DataResolver\Handler\MergeHandlerInterface;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use stdClass;
use tests\Jojo1981\DataResolver\TestCase;
use function array_merge;

/**
 * @package tests\Jojo1981\DataResolver\Extractor
 */
class PropertyExtractorTest extends TestCase
{
    /** @var ObjectProphecy|NamingStrategyInterface */
    private $namingStrategy;

    /** @var ObjectProphecy|PropertyHandlerInterface */
    private $propertyHandler;

    /** @var ObjectProphecy|MergeHandlerInterface */
    private $mergeHandler;

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
        $this->mergeHandler = $this->prophesize(MergeHandlerInterface::class);
        $this->context = $this->prophesize(Context::class);
        $this->context->setData(Argument::any())->shouldNotBeCalled();
        $this->context->setPath(Argument::any())->shouldNotBeCalled();
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws ExtractorException
     */
    public function extractShouldThrowAnExceptionBecausePropertyHandlerDoesNotSupportThePropertyAndDataFromContext(
    ): void {
        $propertyName = 'property-name';
        $this->mergeHandler->merge(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->context->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->context->getPath()->willReturn('my-path')->shouldBeCalledOnce();
        $this->propertyHandler->supports($propertyName, 'my-data')->willReturn(false)->shouldBeCalledOnce();
        $this->propertyHandler->hasValueForPropertyName(
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldNotBeCalled();

        $this->expectExceptionObject(new ExtractorException('Could not extract data with `' . PropertyExtractor::class . '` for property: `property-name` at path: `my-path`'));

        $this->getPropertyExtractor($propertyName)->extract($this->context->reveal());
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws ExtractorException
     */
    public function extractShouldThrowAnExceptionBecausePropertyHandlerSupportThePropertyAndDataFromContextButHasNoValueForThePropertyName(
    ): void {
        $propertyName = 'property-name';
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
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ExtractorException
     * @throws HandlerException
     */
    public function extractShouldReturnTheResultFromThePropertyHandlerGetValueForPropertyNameMethod(): void
    {
        $propertyName = 'the-prop';
        $this->mergeHandler->merge(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->context->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->context->getPath()->shouldNotBeCalled();
        $this->context->pushPathPart($propertyName)->shouldBeCalledOnce();
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

        $this->assertEquals(
            'returned-value',
            $this->getPropertyExtractor($propertyName)->extract($this->context->reveal())
        );
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     */
    public function extractWithMultiplePropertiesShouldReturnTheResultFromTheMergeHandler(): void
    {
        $propertyNames = ['prop1', 'prop2'];
        $resolvedValues = ['value1', 'value2'];

        $this->context->popPathPart()->shouldBeCalledTimes(2);
        $this->context->getData()->willReturn('my-data')->shouldBeCalledTimes(4);

        $this->context->getPath()->shouldNotBeCalled();
        foreach ($propertyNames as $index => $propertyName) {
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

        $this->assertSame($result, $this->getPropertyExtractor(...$propertyNames)->extract($this->context->reveal()));
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
