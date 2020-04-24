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

use Jojo1981\DataResolver\Comparator\ComparatorInterface;
use Jojo1981\DataResolver\Predicate\InPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use tests\Jojo1981\DataResolver\TestCase;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
class InPredicateTest extends TestCase
{
    /** @var ObjectProphecy|ComparatorInterface */
    private $comparator;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->comparator = $this->prophesize(ComparatorInterface::class);
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function matchShouldReturnFalseWhenValueDoesNotExistsInExpectedValues(): void
    {
        $this->comparator->isEqual('value1', 'value2')->willReturn(false)->shouldBeCalledOnce();
        $this->comparator->isEqual('value3', 'value2')->willReturn(false)->shouldBeCalledOnce();

        $this->assertFalse($this->getInPredicate(['value1', 'value3'])->match(new Context('value2')));
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function matchShouldReturnTrueWhenValueDoesExistsInExpectedValuesTest1(): void
    {
        $this->comparator->isEqual('value1', 'value1')->willReturn(true)->shouldBeCalledOnce();

        $this->assertTrue($this->getInPredicate(['value1', 'value2', 'value3'])->match(new Context('value1')));
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function matchShouldReturnTrueWhenValueDoesExistsInExpectedValuesTest2(): void
    {
        $this->comparator->isEqual('value1', 'value2')->willReturn(false)->shouldBeCalledOnce();
        $this->comparator->isEqual('value2', 'value2')->willReturn(true)->shouldBeCalledOnce();

        $this->assertTrue($this->getInPredicate(['value1', 'value2', 'value3'])->match(new Context('value2')));
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function matchShouldReturnTrueWhenValueDoesExistsInExpectedValuesTest3(): void
    {
        $this->comparator->isEqual('value1', 'value3')->willReturn(false)->shouldBeCalledOnce();
        $this->comparator->isEqual('value2', 'value3')->willReturn(false)->shouldBeCalledOnce();
        $this->comparator->isEqual('value3', 'value3')->willReturn(true)->shouldBeCalledOnce();

        $this->assertTrue($this->getInPredicate(['value1', 'value2', 'value3'])->match(new Context('value3')));
    }

    /**
     * @param mixed[] $expectedValues
     * @return InPredicate
     * @throws ObjectProphecyException
     */
    private function getInPredicate(array $expectedValues): InPredicate
    {
        return new InPredicate($expectedValues, $this->comparator->reveal());
    }
}
