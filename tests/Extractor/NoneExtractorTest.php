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

use ArrayIterator;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Extractor\NoneExtractor;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Predicate\PredicateInterface;
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
final class NoneExtractorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<SequenceHandlerInterface> */
    private ObjectProphecy $sequenceHandler;

    /** @var ObjectProphecy<PredicateInterface> */
    private ObjectProphecy $predicate;

    /** @var ObjectProphecy<Context> */
    private ObjectProphecy $originalContext;

    /** @var ObjectProphecy<Context> */
    private ObjectProphecy $copiedContext;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws InvalidArgumentException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->predicate = $this->prophesize(PredicateInterface::class);
        $this->sequenceHandler = $this->prophesize(SequenceHandlerInterface::class);
        $this->originalContext = $this->prophesize(Context::class);
        $this->originalContext->setData(Argument::any())->shouldNotBeCalled();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->originalContext->setPath(Argument::any())->shouldNotBeCalled();
        $this->copiedContext = $this->prophesize(Context::class);
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExtractorException
     * @throws HandlerException
     */
    public function testExtractShouldThrowAnExceptionBecauseSequenceHandlerDoesNotSupportTheDataFromContext(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->originalContext->getPath()->willReturn('my-path')->shouldBeCalledOnce();
        $this->sequenceHandler->supports('my-data')->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(
            new ExtractorException('Could not extract data with `' . NoneExtractor::class . '` at path: `my-path`')
        );

        $this->getSomeExtractor()->extract($this->originalContext->reveal());
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     */
    public function testExtractShouldReturnTrueWhenSequenceHandlerGetIteratorReturnAnEmptyIterator(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->copiedContext->setData(Argument::any())->shouldNotBeCalled();
        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->sequenceHandler->getIterator('my-data')->willReturn(new ArrayIterator())->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->originalContext->copy()->shouldNotBeCalled();

        self::assertTrue($this->getSomeExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     */
    public function testExtractShouldReturnFalseWhenAnItemIsMatchedByThePredicate(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledTimes(2);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->copiedContext->pushPathPart('key1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->copiedContext->pushPathPart('key2')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->copiedContext->setData('value1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->copiedContext->setData('value2')->willReturn($this->copiedContext)->shouldBeCalledOnce();

        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->sequenceHandler->getIterator('my-data')->willReturn($this->getTestIterator())->shouldBeCalledOnce();

        $this->predicate->match($this->copiedContext)->willReturn(false, true)->shouldBeCalledTimes(2);

        self::assertFalse($this->getSomeExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     */
    public function testExtractShouldReturnTrueWhenNoItemIsMatchedByThePredicate(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledTimes(3);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->copiedContext->pushPathPart('key1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->copiedContext->pushPathPart('key2')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->copiedContext->pushPathPart('key3')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->copiedContext->setData('value1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->copiedContext->setData('value2')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->copiedContext->setData('value3')->willReturn($this->copiedContext)->shouldBeCalledOnce();

        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->sequenceHandler->getIterator('my-data')->willReturn($this->getTestIterator())->shouldBeCalledOnce();

        $this->predicate->match($this->copiedContext)->willReturn(false, false, false)->shouldBeCalledTimes(3);

        self::assertTrue($this->getSomeExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @return NoneExtractor
     * @throws ObjectProphecyException
     */
    private function getSomeExtractor(): NoneExtractor
    {
        return new NoneExtractor($this->sequenceHandler->reveal(), $this->predicate->reveal());
    }

    /**
     * @return ArrayIterator
     */
    private function getTestIterator(): ArrayIterator
    {
        return new ArrayIterator(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']);
    }
}
