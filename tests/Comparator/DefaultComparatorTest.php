<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Comparator;

use Jojo1981\DataResolver\Comparator\DefaultComparator;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Comparator
 */
class DefaultComparatorTest extends TestCase
{
    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ClassNotFoundException
     * @throws DoubleException
     * @throws InterfaceNotFoundException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @return void
     */
    public function isEqualShouldReturnFalseWhenTheUsedIsEqualConstraintThrowsAnException(): void
    {
        /** @var ObjectProphecy|\SplObjectStorage $objectStorage1 */
        $objectStorage1 = $this->prophesize(\SplObjectStorage::class);

        /** @var ObjectProphecy|\SplObjectStorage $objectStorage2 */
        $objectStorage2 = $this->prophesize(\SplObjectStorage::class);
        $objectStorage2->rewind()->willThrow(new \Exception('Force the IsEqual constraint to throw an exception'));

        $this->assertFalse($this->getDefaultComparator()->isEqual($objectStorage1->reveal(), $objectStorage2->reveal()));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function isEqualShouldReturnFalseWhenValuesAreNotEqual(): void
    {
        $this->assertFalse($this->getDefaultComparator()->isEqual('value1', 'value2'));
        $this->assertFalse($this->getDefaultComparator()->isEqual('Value1', 'value1'));
        $this->assertFalse($this->getDefaultComparator()->isEqual(true, false));
        $this->assertFalse($this->getDefaultComparator()->isEqual(false, true));
        $this->assertFalse($this->getDefaultComparator()->isEqual(['name' => 'Tester'], ['name' => 'tester']));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function isEqualShouldReturnTrueWhenValuesAreEqual(): void
    {
        $this->assertTrue($this->getDefaultComparator()->isEqual('value1', 'value1'));
        $this->assertTrue($this->getDefaultComparator()->isEqual(['value1'], ['value1']));
        $this->assertTrue($this->getDefaultComparator()->isEqual(new \stdClass(), new \stdClass()));
        $this->assertTrue($this->getDefaultComparator()->isEqual(['name' => 'Tester'], ['name' => 'Tester']));
        $this->assertTrue($this->getDefaultComparator()->isEqual(true, true));
        $this->assertTrue($this->getDefaultComparator()->isEqual(false, false));
        $this->assertTrue($this->getDefaultComparator()->isEqual(
            ['name' => 'my-name', 'age' => 'my-age'],
            ['age' => 'my-age', 'name' => 'my-name']
        ));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function isGreaterThanShouldReturnFalseWhenValueToCompareIsNotGreaterThanTheReferenceValue(): void
    {
        $this->assertFalse($this->getDefaultComparator()->isGreaterThan(-4, -4));
        $this->assertFalse($this->getDefaultComparator()->isGreaterThan(0, 0));
        $this->assertFalse($this->getDefaultComparator()->isGreaterThan(1, 1));
        $this->assertFalse($this->getDefaultComparator()->isGreaterThan(10, 8));
        $this->assertFalse($this->getDefaultComparator()->isGreaterThan(false, false));
        $this->assertFalse($this->getDefaultComparator()->isGreaterThan(true, false));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function isGreaterThanShouldReturnTrueWhenValueToCompareIsGreaterThanTheReferenceValue(): void
    {
        $this->assertTrue($this->getDefaultComparator()->isGreaterThan(-4, 0));
        $this->assertTrue($this->getDefaultComparator()->isGreaterThan(0, 1));
        $this->assertTrue($this->getDefaultComparator()->isGreaterThan(1, 2));
        $this->assertTrue($this->getDefaultComparator()->isGreaterThan(8, 10));
        $this->assertTrue($this->getDefaultComparator()->isGreaterThan(false, true));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function isLessThanShouldReturnFalseWhenValueToCompareIsNotLessThanTheReferenceValue(): void
    {
        $this->assertFalse($this->getDefaultComparator()->isLessThan(-4, -4));
        $this->assertFalse($this->getDefaultComparator()->isLessThan(0, 0));
        $this->assertFalse($this->getDefaultComparator()->isLessThan(1, 1));
        $this->assertFalse($this->getDefaultComparator()->isLessThan(8, 10));
        $this->assertFalse($this->getDefaultComparator()->isLessThan(false, false));
        $this->assertFalse($this->getDefaultComparator()->isLessThan(false, true));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function isLessThanShouldReturnTrueWhenValueToCompareIsLessThanTheReferenceValue(): void
    {
        $this->assertTrue($this->getDefaultComparator()->isLessThan(0, -4));
        $this->assertTrue($this->getDefaultComparator()->isLessThan(1, 0));
        $this->assertTrue($this->getDefaultComparator()->isLessThan(2, 1));
        $this->assertTrue($this->getDefaultComparator()->isLessThan(10, 8));
        $this->assertTrue($this->getDefaultComparator()->isLessThan(true, false));
//        $this->assertTrue($this->getDefaultComparator()->isLessThan('longer', 'short'));
    }

    /**
     * @return DefaultComparator
     */
    private function getDefaultComparator(): DefaultComparator
    {
        return new DefaultComparator();
    }
}