<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Predicate;

use ArrayIterator;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Predicate\PredicateInterface;
use Jojo1981\DataResolver\Predicate\SomePredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
final class SomePredicateTest extends TestCase
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
        $this->sequenceHandler = $this->prophesize(SequenceHandlerInterface::class);
        $this->predicate = $this->prophesize(PredicateInterface::class);
        $this->originalContext = $this->prophesize(Context::class);
        $this->copiedContext = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @return void
     * @throws PredicateException
     * @throws ObjectProphecyException
     * @throws ExtractorException
     * @throws HandlerException
     */
    public function matchShouldThrowAnExceptionBecauseSequenceHandlerDoesNotSupportTheDataFromContext(): void
    {
        $this->originalContext->getData()->willReturn('my-data-1')->shouldBeCalledTimes(1);
        $this->originalContext->getPath()->willReturn('a-path-value')->shouldBeCalled();
        $this->sequenceHandler->supports(Argument::exact('my-data-1'))->willReturn(false)->shouldBeCalled();
        $this->sequenceHandler->getIterator(Argument::any())->shouldNotBeCalled();
        $this->predicate->match(Argument::any())->shouldNotBeCalled();

        $this->expectExceptionObject(new PredicateException('Could not match data with `' . SomePredicate::class . '` at path: `a-path-value`'));

        $this->getSomePredicate()->match($this->originalContext->reveal());
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
    public function matchShouldReturnFalseWhenDataIsAnEmptyArray(): void
    {
        $this->originalContext->getData()->willReturn('my-data-2')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();

        $this->sequenceHandler->supports(Argument::exact('my-data-2'))->willReturn(true)->shouldBeCalled();
        $this->sequenceHandler->getIterator(Argument::exact('my-data-2'))->willReturn(new ArrayIterator())->shouldBeCalled();

        $this->predicate->match(Argument::any())->shouldNotBeCalled();

        $this->assertFalse($this->getSomePredicate()->match($this->originalContext->reveal()));
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
    public function matchShouldReturnTrueAsSoonAsPossibleThusWhenAnItemIsMatching(): void
    {
        $this->originalContext->getData()->willReturn('my-data-2')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledTimes(2);
        $this->copiedContext->pushPathPart('key1')->willReturn($this->copiedContext)->shouldBeCalled();
        $this->copiedContext->pushPathPart('key2')->willReturn($this->copiedContext)->shouldBeCalled();
        $this->copiedContext->setData('value1')->willReturn($this->copiedContext)->shouldBeCalled();
        $this->copiedContext->setData('value2')->willReturn($this->copiedContext)->shouldBeCalled();

        $iterator = new ArrayIterator(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']);
        $this->sequenceHandler->supports(Argument::exact('my-data-2'))->willReturn(true)->shouldBeCalled();
        $this->sequenceHandler->getIterator(Argument::exact('my-data-2'))->willReturn($iterator)->shouldBeCalled();

        $this->predicate->match($this->copiedContext)->willReturn(false, true)->shouldBeCalledTimes(2);

        $this->assertTrue($this->getSomePredicate()->match($this->originalContext->reveal()));
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
    public function matchShouldReturnFalseWhenAllItemsAreNotMatching(): void
    {
        $this->originalContext->getData()->willReturn('my-data-3')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledTimes(3);
        $this->copiedContext->pushPathPart('key1')->willReturn($this->copiedContext)->shouldBeCalled();
        $this->copiedContext->pushPathPart('key2')->willReturn($this->copiedContext)->shouldBeCalled();
        $this->copiedContext->pushPathPart('key3')->willReturn($this->copiedContext)->shouldBeCalled();
        $this->copiedContext->setData('value1')->willReturn($this->copiedContext)->shouldBeCalled();
        $this->copiedContext->setData('value2')->willReturn($this->copiedContext)->shouldBeCalled();
        $this->copiedContext->setData('value3')->willReturn($this->copiedContext)->shouldBeCalled();

        $iterator = new ArrayIterator(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']);
        $this->sequenceHandler->supports(Argument::exact('my-data-3'))->willReturn(true)->shouldBeCalled();
        $this->sequenceHandler->getIterator(Argument::exact('my-data-3'))->willReturn($iterator)->shouldBeCalled();

        $this->predicate->match($this->copiedContext)->willReturn(false, false, false)->shouldBeCalledTimes(3);

        $this->assertFalse($this->getSomePredicate()->match($this->originalContext->reveal()));
    }

    /**
     * @return SomePredicate
     * @throws ObjectProphecyException
     */
    private function getSomePredicate(): SomePredicate
    {
        return new SomePredicate($this->sequenceHandler->reveal(), $this->predicate->reveal());
    }
}
