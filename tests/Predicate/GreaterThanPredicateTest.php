<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
declare(strict_types=1);

namespace tests\Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Comparator\ComparatorInterface;
use Jojo1981\DataResolver\Predicate\GreaterThanPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\InvalidArgumentException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
final class GreaterThanPredicateTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<ComparatorInterface> */
    private ObjectProphecy $comparator;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws InvalidArgumentException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->comparator = $this->prophesize(ComparatorInterface::class);
        $this->comparator->isEqual(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->comparator->isLessThan(Argument::any(), Argument::any())->shouldNotBeCalled();
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function testMatchShouldReturnTrueFromComparator(): void
    {
        $referenceValue = 'dummy1';
        $toCompareValue = 'dummy2';
        $this->comparator->isGreaterThan($referenceValue, $toCompareValue)->willReturn(true)->shouldBeCalledOnce();
        self::assertTrue($this->getGreaterThanPredicate($referenceValue)->match(new Context($toCompareValue)));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function testMatchShouldReturnFalseFromComparator(): void
    {
        $referenceValue = 'dummy1';
        $toCompareValue = 'dummy2';
        $this->comparator->isGreaterThan($referenceValue, $toCompareValue)->willReturn(false)->shouldBeCalledOnce();
        self::assertFalse($this->getGreaterThanPredicate($referenceValue)->match(new Context($toCompareValue)));
    }

    /**
     * @param mixed $referenceValue
     * @return GreaterThanPredicate
     * @throws ObjectProphecyException
     */
    private function getGreaterThanPredicate(mixed $referenceValue): GreaterThanPredicate
    {
        return new GreaterThanPredicate($this->comparator->reveal(), $referenceValue);
    }
}
