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

use Exception;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Extractor\FilterExtractor;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Predicate\PredicateInterface;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\InvalidArgumentException as ProphecyInvalidArgumentException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use function call_user_func;
use function is_callable;

/**
 * @package tests\Jojo1981\DataResolver\Extractor
 */
final class FilterExtractorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|SequenceHandlerInterface */
    private ObjectProphecy $sequenceHandler;

    /** @var ObjectProphecy|PredicateInterface */
    private ObjectProphecy $predicate;

    /** @var ObjectProphecy|Context */
    private ObjectProphecy $originalContext;

    /** @var ObjectProphecy|Context */
    private ObjectProphecy $copiedContext;

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

        $this->expectExceptionObject(new ExtractorException('Could not extract data with `' . FilterExtractor::class . '` at path: `my-path`'));

        $this->getFilterExtractor()->extract($this->originalContext->reveal());
    }

    /**
     * @test
     *
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ProphecyInvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function extractShouldCatchExceptionThrownByPredicateAndConsiderThePredicateAsFalse(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('my-value-1')->willReturn($this->copiedContext)->shouldBeCalledOnce();

        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->predicate->match($this->copiedContext)->willThrow(Exception::class)->shouldBeCalledOnce();

        $this->sequenceHandler->filter('my-data', Argument::that(function ($arg): bool {
            if (is_callable($arg)) {
                $this->assertFalse(call_user_func($arg, 'my-value-1'));

                return true;
            }

            return false;
        }))->willReturn('extracted-data')->shouldBeCalledOnce();

        $this->assertEquals('extracted-data', $this->getFilterExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @test
     *
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ProphecyInvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function extractShouldReturnTheResultFromTheSequenceHandlerFilterMethod(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledTimes(2);
        $this->copiedContext->setData('my-value-1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('my-value-2')->willReturn($this->copiedContext)->shouldBeCalledOnce();

        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->predicate->match($this->copiedContext)->willReturn(false, true)->shouldBeCalledTimes(2);

        $this->sequenceHandler->filter('my-data', Argument::that(function ($arg): bool {
            if (is_callable($arg)) {
                $this->assertFalse(call_user_func($arg, 'my-value-1'));
                $this->assertTrue(call_user_func($arg, 'my-value-2'));

                return true;
            }

            return false;
        }))->willReturn('extracted-data')->shouldBeCalledOnce();

        $this->assertEquals('extracted-data', $this->getFilterExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @return FilterExtractor
     * @throws ObjectProphecyException
     */
    private function getFilterExtractor(): FilterExtractor
    {
        return new FilterExtractor($this->sequenceHandler->reveal(), $this->predicate->reveal());
    }
}
