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
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Extractor\FlattenExtractor;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
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
class FlattenExtractorTest extends TestCase
{
    /** @var ObjectProphecy|SequenceHandlerInterface */
    private $sequenceHandler;

    /** @var ObjectProphecy|ExtractorInterface */
    private $extractor;

    /** @var ObjectProphecy|Context */
    private $originalContext;

    /** @var ObjectProphecy|Context */
    private $copiedContext;

    /**
     * @throws DoubleException
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @return void
     */
    protected function setUp(): void
    {
        $this->extractor = $this->prophesize(ExtractorInterface::class);
        $this->sequenceHandler = $this->prophesize(SequenceHandlerInterface::class);
        $this->originalContext = $this->prophesize(Context::class);
        $this->copiedContext = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExtractorException
     * @return void
     */
    public function extractShouldThrowAnExceptionBecauseSequenceHandlerDoesNotSupportTheDataFromContext(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->originalContext->getPath()->willReturn('my-path')->shouldBeCalledOnce();
        $this->sequenceHandler->supports('my-data')->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(new ExtractorException('Could not extract data with `' . FlattenExtractor::class . '` at path: `my-path`'));

        $this->getFlattenExtractor()->extract($this->originalContext->reveal());
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ExtractorException
     * @return void
     */
    public function extractShouldReturnTheResultFromTheSequenceHandlerFlattenMethod(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->pushPathPart('my-key-1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData( 'my-value-1')->willReturn($this->copiedContext)->shouldBeCalledOnce();

        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->extractor->extract($this->copiedContext)->shouldBeCalledOnce();

        $this->sequenceHandler->flatten('my-data', Argument::that(static function ($arg): bool {
            if (\is_callable($arg)) {
                \call_user_func($arg, 'my-key-1', 'my-value-1');

                return true;
            }

            return false;
        }))->willReturn('extracted-data')->shouldBeCalledOnce();

        $this->assertEquals('extracted-data', $this->getFlattenExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @throws ObjectProphecyException
     * @return FlattenExtractor
     */
    private function getFlattenExtractor(): FlattenExtractor
    {
        return new FlattenExtractor($this->sequenceHandler->reveal(), $this->extractor->reveal());
    }
}