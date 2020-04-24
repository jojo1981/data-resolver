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
use Jojo1981\DataResolver\Predicate\EqualsPredicate;
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
class EqualsPredicateTest extends TestCase
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
        $this->comparator->isGreaterThan(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->comparator->isLessThan(Argument::any(), Argument::any())->shouldNotBeCalled();
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function matchShouldReturnTrueFromComparator(): void
    {
        $referenceValue = 'dummy1';
        $toCompareValue = 'dummy2';
        $this->comparator->isEqual($referenceValue, $toCompareValue)->willReturn(true)->shouldBeCalledOnce();
        $this->assertTrue($this->getEqualsPredicate($referenceValue)->match(new Context($toCompareValue)));
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function matchShouldReturnFalseFromComparator(): void
    {
        $referenceValue = 'dummy1';
        $toCompareValue = 'dummy2';
        $this->comparator->isEqual($referenceValue, $toCompareValue)->willReturn(false)->shouldBeCalledOnce();
        $this->assertFalse($this->getEqualsPredicate($referenceValue)->match(new Context($toCompareValue)));
    }

    /**
     * @param mixed $referenceValue
     * @return EqualsPredicate
     * @throws ObjectProphecyException
     */
    private function getEqualsPredicate($referenceValue): EqualsPredicate
    {
        return new EqualsPredicate($this->comparator->reveal(), $referenceValue);
    }
}
