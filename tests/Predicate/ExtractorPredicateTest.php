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
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Predicate\ExtractorPredicate;
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
class ExtractorPredicateTest extends TestCase
{
    /** @var ObjectProphecy|ExtractorInterface */
    private $extractor;

    /** @var ObjectProphecy|PredicateInterface */
    private $predicate;

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
        $this->predicate = $this->prophesize(PredicateInterface::class);
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
    public function matchShouldReturnFalseBecauseTheTheExtractorThrowsAnException(): void
    {
        $this->predicate->match($this->copiedContext)->willThrow(\Exception::class)->shouldBeCalled();

        $this->assertFalse($this->getExtractorPredicate()->match($this->originalContext->reveal()));
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
    public function matchShouldReturnFalseBecauseThePredicateReturnFalseWhenMatchingTheReturnedValueOfTheExtractor(): void
    {
        $this->predicate->match($this->copiedContext)->willReturn(false)->shouldBeCalled();

        $this->assertFalse($this->getExtractorPredicate()->match($this->originalContext->reveal()));
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
    public function matchShouldReturnTrueBecauseThePredicateReturnTrueWhenMatchingTheReturnedValueOfTheExtractor(): void
    {
        $this->predicate->match($this->copiedContext)->willReturn(true)->shouldBeCalled();

        $this->assertTrue($this->getExtractorPredicate()->match($this->originalContext->reveal()));
    }

    /**
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExtractorException
     * @return ExtractorPredicate
     */
    private function getExtractorPredicate(): ExtractorPredicate
    {
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalled();
        $this->originalContext->setData(Argument::any())->shouldNotBeCalled();
        $this->copiedContext->setData('my-extracted-data')->willReturn($this->copiedContext)->shouldBeCalled();

        $this->extractor->extract($this->originalContext)->willReturn('my-extracted-data')->shouldBeCalled();

        return new ExtractorPredicate($this->extractor->reveal(), $this->predicate->reveal());
    }
}