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
use PHPUnit\Framework\TestCase;
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
     * @throws DoubleException
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @return void
     */
    protected function setUp(): void
    {
        $this->comparator = $this->prophesize(ComparatorInterface::class);
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
        $expectedValue = new \stdClass();
        $actualValue = [];
        $this->comparator->isEqual($expectedValue, $actualValue)->willReturn(true)->shouldBeCalledOnce();
        $this->assertTrue($this->getEqualsPredicate($expectedValue)->match(new Context($actualValue)));
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
        $expectedValue = 'text1';
        $actualValue = new \stdClass();
        $this->comparator->isEqual($expectedValue, $actualValue)->willReturn(false)->shouldBeCalledOnce();
        $this->assertFalse($this->getEqualsPredicate($expectedValue)->match(new Context($actualValue)));
    }

    /**
     * @param mixed $expectedValue
     * @throws ObjectProphecyException
     * @return EqualsPredicate
     */
    private function getEqualsPredicate($expectedValue): EqualsPredicate
    {
        return new EqualsPredicate($expectedValue, $this->comparator->reveal());
    }
}