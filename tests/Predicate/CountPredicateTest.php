<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\CountPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException as PHPUnitExpectationFailedException;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException as SebastianBergmannInvalidArgumentException;
use tests\Jojo1981\DataResolver\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
class CountPredicateTest extends TestCase
{
    /** @var ObjectProphecy|SequenceHandlerInterface */
    private $sequenceHandler;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->sequenceHandler = $this->prophesize(SequenceHandlerInterface::class);
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws PHPUnitExpectationFailedException
     * @throws SebastianBergmannInvalidArgumentException
     * @throws ObjectProphecyException
     */
    public function matchShouldReturnTheCorrectResult(): void
    {
        $this->sequenceHandler->count('text1')->willReturn(1)->shouldBeCalledOnce();
        $this->sequenceHandler->count('text2')->willReturn(4)->shouldBeCalledOnce();
        $this->assertTrue($this->getIsEmptyPredicate(1)->match(new Context('text1')));
        $this->assertFalse($this->getIsEmptyPredicate(1)->match(new Context('text2')));
    }

    /**
     * @param int $expectedCount
     * @return CountPredicate
     * @throws ObjectProphecyException
     */
    private function getIsEmptyPredicate(int $expectedCount): CountPredicate
    {
        return new CountPredicate($this->sequenceHandler->reveal(), $expectedCount);
    }
}
