<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\NonePredicate;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Predicate\PredicateInterface;
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
 * @package tests\Jojo1981\DataResolver\Predicate
 */
class NonePredicateTest extends TestCase
{
    /** @var ObjectProphecy|SequenceHandlerInterface */
    private $sequenceHandler;

    /** @var ObjectProphecy|PredicateInterface */
    private $predicate;

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
        $this->sequenceHandler = $this->prophesize(SequenceHandlerInterface::class);
        $this->predicate = $this->prophesize(PredicateInterface::class);
        $this->context = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws PredicateException
     * @throws ObjectProphecyException
     * @throws ExtractorException
     * @return void
     */
    public function matchShouldThrowAnExceptionBecauseSequenceHandlerDoesNotSupportTheDataFromContext(): void
    {
        $this->context->getData()->willReturn('my-data-1')->shouldBeCalledTimes(1);
        $this->context->getPath()->willReturn('a-path-value')->shouldBeCalled();
        $this->sequenceHandler->supports(Argument::exact('my-data-1'))->willReturn(false)->shouldBeCalled();
        $this->sequenceHandler->getIterator(Argument::any())->shouldNotBeCalled();
        $this->predicate->match(Argument::any())->shouldNotBeCalled();

        $this->expectExceptionObject(new PredicateException('Could not match data with `' . NonePredicate::class . '` at path: `a-path-value`'));

        $this->getNonePredicate()->match($this->context->reveal());
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnTrueWhenDataIsAnEmptyArray(): void
    {
        $this->context->getData()->willReturn('my-data-2')->shouldBeCalledTimes(2);
        $this->context->getPath()->shouldNotBeCalled();

        $this->sequenceHandler->supports(Argument::exact('my-data-2'))->willReturn(true)->shouldBeCalled();
        $this->sequenceHandler->getIterator(Argument::exact('my-data-2'))->willReturn(new \ArrayIterator())->shouldBeCalled();

        $this->predicate->match(Argument::any())->shouldNotBeCalled();

        $this->assertTrue($this->getNonePredicate()->match($this->context->reveal()));
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnTrueAsSoonAsPossibleThusWhenAnItemIsMatching(): void
    {
        $this->context->getData()->willReturn('my-data-2')->shouldBeCalledTimes(2);
        $this->context->getPath()->shouldNotBeCalled();
        $this->context->copy()->willReturn($this->context)->shouldBeCalledTimes(2);
        $this->context->pushPathPart('key1')->willReturn($this->context)->shouldBeCalled();
        $this->context->pushPathPart('key2')->willReturn($this->context)->shouldBeCalled();
        $this->context->setData('value1')->willReturn($this->context)->shouldBeCalled();
        $this->context->setData('value2')->willReturn($this->context)->shouldBeCalled();

        $iterator = new \ArrayIterator(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']);
        $this->sequenceHandler->supports(Argument::exact('my-data-2'))->willReturn(true)->shouldBeCalled();
        $this->sequenceHandler->getIterator(Argument::exact('my-data-2'))->willReturn($iterator)->shouldBeCalled();

        $this->predicate->match($this->context)->willReturn(false, true)->shouldBeCalledTimes(2);

        $this->assertFalse($this->getNonePredicate()->match($this->context->reveal()));
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnTrueWhenAllItemsAreNotMatching(): void
    {
        $this->context->getData()->willReturn('my-data-3')->shouldBeCalledTimes(2);
        $this->context->getPath()->shouldNotBeCalled();
        $this->context->copy()->willReturn($this->context)->shouldBeCalledTimes(3);
        $this->context->pushPathPart('key1')->willReturn($this->context)->shouldBeCalled();
        $this->context->pushPathPart('key2')->willReturn($this->context)->shouldBeCalled();
        $this->context->pushPathPart('key3')->willReturn($this->context)->shouldBeCalled();
        $this->context->setData('value1')->willReturn($this->context)->shouldBeCalled();
        $this->context->setData('value2')->willReturn($this->context)->shouldBeCalled();
        $this->context->setData('value3')->willReturn($this->context)->shouldBeCalled();

        $iterator = new \ArrayIterator(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']);
        $this->sequenceHandler->supports(Argument::exact('my-data-3'))->willReturn(true)->shouldBeCalled();
        $this->sequenceHandler->getIterator(Argument::exact('my-data-3'))->willReturn($iterator)->shouldBeCalled();

        $this->predicate->match($this->context)->willReturn(false, false, false)->shouldBeCalledTimes(3);

        $this->assertTrue($this->getNonePredicate()->match($this->context->reveal()));
    }

    /**
     * @throws ObjectProphecyException
     * @return NonePredicate
     */
    private function getNonePredicate(): NonePredicate
    {
        return new NonePredicate($this->sequenceHandler->reveal(), $this->predicate->reveal());
    }
}