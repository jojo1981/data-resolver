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
use Jojo1981\DataResolver\Predicate\AndPredicate;
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
 * @package tests\Jojo1981\DataResolver\Predicate
 */
class AndPredicateTest extends TestCase
{
    /** @var ObjectProphecy|PredicateInterface */
    private $leftPredicate;

    /** @var ObjectProphecy|PredicateInterface */
    private $rightPredicate;

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
        $this->leftPredicate = $this->prophesize(PredicateInterface::class);
        $this->rightPredicate = $this->prophesize(PredicateInterface::class);
        $this->originalContext = $this->prophesize(Context::class);
        $this->copiedContext = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @return void
     */
    public function matchShouldReturnFalseBecauseLeftPredicateReturnFalseAndRightPredicateShouldNotBeCalled(): void
    {
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->leftPredicate->match($this->copiedContext)->willReturn(false)->shouldBeCalledOnce();
        $this->rightPredicate->match(Argument::any())->shouldNotBeCalled();

        $this->assertFalse($this->getAndPredicate()->match($this->originalContext->reveal()));
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @return void
     */
    public function matchShouldReturnFalseBecauseLeftPredicateReturnTrueAndRightPredicateReturnFalse(): void
    {
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledTimes(2);
        $this->leftPredicate->match($this->copiedContext)->willReturn(true)->shouldBeCalledOnce();
        $this->rightPredicate->match($this->copiedContext)->willReturn(false)->shouldBeCalledOnce();

        $this->assertFalse($this->getAndPredicate()->match($this->originalContext->reveal()));
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @return void
     */
    public function matchShouldReturnTrueBecauseLeftPredicateAndRightPredicateReturnTrue(): void
    {
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledTimes(2);
        $this->leftPredicate->match($this->copiedContext)->willReturn(true)->shouldBeCalledOnce();
        $this->rightPredicate->match($this->copiedContext)->willReturn(true)->shouldBeCalledOnce();

        $this->assertTrue($this->getAndPredicate()->match($this->originalContext->reveal()));
    }

    /**
     * @throws ObjectProphecyException
     * @return AndPredicate
     */
    private function getAndPredicate(): AndPredicate
    {
        return new AndPredicate($this->leftPredicate->reveal(), $this->rightPredicate->reveal());
    }
}