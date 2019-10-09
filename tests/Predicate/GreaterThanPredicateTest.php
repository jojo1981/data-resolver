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
use Jojo1981\DataResolver\Predicate\GreaterThanPredicate;
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
class GreaterThanPredicateTest extends TestCase
{
    /** @var ObjectProphecy|ComparatorInterface */
    private $comparator;

    /**
     * @throws DoubleException
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @return void
     */
    protected function setUp(): void
    {
        $this->comparator = $this->prophesize(ComparatorInterface::class);
        $this->comparator->isEqual(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->comparator->isLessThan(Argument::any(), Argument::any())->shouldNotBeCalled();
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnTrueFromComparator(): void
    {
        $referenceValue = 'dummy1';
        $toCompareValue = 'dummy2';
        $this->comparator->isGreaterThan($referenceValue, $toCompareValue)->willReturn(true)->shouldBeCalledOnce();
        $this->assertTrue($this->getGreaterThanPredicate($referenceValue)->match(new Context($toCompareValue)));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnFalseFromComparator(): void
    {
        $referenceValue = 'dummy1';
        $toCompareValue = 'dummy2';
        $this->comparator->isGreaterThan($referenceValue, $toCompareValue)->willReturn(false)->shouldBeCalledOnce();
        $this->assertFalse($this->getGreaterThanPredicate($referenceValue)->match(new Context($toCompareValue)));
    }

    /**
     * @param mixed $referenceValue
     * @throws ObjectProphecyException
     * @return GreaterThanPredicate
     */
    private function getGreaterThanPredicate($referenceValue): GreaterThanPredicate
    {
        return new GreaterThanPredicate($this->comparator->reveal(), $referenceValue);
    }
}