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

use ArrayIterator;
use Jojo1981\DataResolver\Extractor\AllExtractor;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Predicate\PredicateInterface;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use tests\Jojo1981\DataResolver\TestCase;
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
class AllExtractorTest extends TestCase
{
    /** @var ObjectProphecy|SequenceHandlerInterface */
    private $sequenceHandler;

    /** @var ObjectProphecy|PredicateInterface */
    private $predicate;

    /** @var ObjectProphecy|Context */
    private $originalContext;

    /** @var ObjectProphecy|Context */
    private $copiedContext;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->predicate = $this->prophesize(PredicateInterface::class);
        $this->sequenceHandler = $this->prophesize(SequenceHandlerInterface::class);
        $this->originalContext = $this->prophesize(Context::class);
        $this->originalContext->setData(Argument::any())->shouldNotBeCalled();
        $this->originalContext->setPath(Argument::any())->shouldNotBeCalled();
        $this->copiedContext = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExtractorException
     * @throws HandlerException
     */
    public function extractShouldThrowAnExceptionBecauseSequenceHandlerDoesNotSupportTheDataFromContext(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->originalContext->getPath()->willReturn('my-path')->shouldBeCalledOnce();
        $this->sequenceHandler->supports('my-data')->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(
            new ExtractorException('Could not extract data with `' . AllExtractor::class . '` at path: `my-path`')
        );

        $this->getAllExtractor()->extract($this->originalContext->reveal());
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     */
    public function extractShouldReturnTrueWhenSequenceHandlerGetIteratorReturnAnEmptyIterator(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->copiedContext->setData(Argument::any())->shouldNotBeCalled();
        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler->getIterator('my-data')->willReturn(new ArrayIterator())->shouldBeCalledOnce();
        $this->originalContext->copy()->shouldNotBeCalled();

        $this->assertTrue($this->getAllExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     */
    public function extractShouldReturnFalseWhenAnItemIsNotMatchedByThePredicate(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledTimes(2);

        $this->copiedContext->pushPathPart('key1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->pushPathPart('key2')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('value1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('value2')->willReturn($this->copiedContext)->shouldBeCalledOnce();

        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler->getIterator('my-data')->willReturn($this->getTestIterator())->shouldBeCalledOnce();

        $this->predicate->match($this->copiedContext)->willReturn(true, false)->shouldBeCalledTimes(2);

        $this->assertFalse($this->getAllExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     */
    public function extractShouldReturnTrueWhenAllItemsAreMatchedByThePredicate(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledTimes(3);

        $this->copiedContext->pushPathPart('key1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->pushPathPart('key2')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->pushPathPart('key3')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('value1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('value2')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('value3')->willReturn($this->copiedContext)->shouldBeCalledOnce();

        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler->getIterator('my-data')->willReturn($this->getTestIterator())->shouldBeCalledOnce();

        $this->predicate->match($this->copiedContext)->willReturn(true, true, true)->shouldBeCalledTimes(3);

        $this->assertTrue($this->getAllExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @return AllExtractor
     * @throws ObjectProphecyException
     */
    private function getAllExtractor(): AllExtractor
    {
        return new AllExtractor($this->sequenceHandler->reveal(), $this->predicate->reveal());
    }

    /**
     * @return ArrayIterator
     */
    private function getTestIterator(): ArrayIterator
    {
        return new ArrayIterator(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']);
    }
}
