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

use ArrayIterator;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Extractor\FindExtractor;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Predicate\PredicateInterface;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use tests\Jojo1981\DataResolver\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Extractor
 */
class FindExtractorTest extends TestCase
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
        $this->originalContext->setData(Argument::any())->shouldNotBeCalled();
        $this->sequenceHandler->supports('my-data')->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(new ExtractorException('Could not extract data with `' . FindExtractor::class . '` at path: `my-path`'));

        $this->getFindExtractor()->extract($this->originalContext->reveal());
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
    public function extractShouldReturnNullWhenSequenceHandlerGetIteratorReturnAnEmptyIterator(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->originalContext->setData(Argument::any())->shouldNotBeCalled();
        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler->getIterator('my-data')->willReturn(new ArrayIterator())->shouldBeCalledOnce();
        $this->originalContext->copy()->shouldNotBeCalled();

        $this->assertNull($this->getFindExtractor()->extract($this->originalContext->reveal()));
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
    public function extractShouldReturnNullWhenNoItemIsMatchedByThePredicate(): void
    {
        $iterator = new ArrayIterator(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']);
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->originalContext->setData(Argument::any())->shouldNotBeCalled();
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledTimes(3);

        $this->copiedContext->pushPathPart('key1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->pushPathPart('key2')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->pushPathPart('key3')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('value1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('value2')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('value3')->willReturn($this->copiedContext)->shouldBeCalledOnce();

        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler->getIterator('my-data')->willReturn($iterator)->shouldBeCalledOnce();

        $this->predicate->match($this->copiedContext)->willReturn(false, false, false)->shouldBeCalledTimes(3);

        $this->assertNull($this->getFindExtractor()->extract($this->originalContext->reveal()));
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
    public function extractShouldReturnTheFirstItemWhichIsMatchedByThePredicate(): void
    {
        $iterator = new ArrayIterator(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']);
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->originalContext->setData(Argument::any())->shouldNotBeCalled();
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledTimes(2);

        $this->copiedContext->pushPathPart('key1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->pushPathPart('key2')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->pushPathPart('key3')->shouldNotBeCalled();
        $this->copiedContext->setData('value1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('value2')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('value3')->shouldNotBeCalled();

        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler->getIterator('my-data')->willReturn($iterator)->shouldBeCalledOnce();

        $this->predicate->match($this->copiedContext)->willReturn(false, true)->shouldBeCalledTimes(2);

        $this->assertEquals('value2', $this->getFindExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @return FindExtractor
     * @throws ObjectProphecyException
     */
    private function getFindExtractor(): FindExtractor
    {
        return new FindExtractor($this->sequenceHandler->reveal(), $this->predicate->reveal());
    }
}
