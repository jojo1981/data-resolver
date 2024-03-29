<?php declare(strict_types=1);
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
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;
use SebastianBergmann\Comparator\RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use stdClass;

/**
 * @package tests\Jojo1981\DataResolver\Comparator
 */
final class DefaultComparatorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ComparatorFactory|ObjectProphecy */
    private ObjectProphecy $comparatorFactory;

    /** @var Comparator|ObjectProphecy */
    private ObjectProphecy $comparator;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->comparatorFactory = $this->prophesize(ComparatorFactory::class);
        $this->comparator = $this->prophesize(Comparator::class);
    }

    /**
     * @test
     *
     * @return void
     * @throws RuntimeException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function isEqualShouldReturnFalseWhenComparatorFactoryHasNoComparatorFor(): void
    {
        $this->comparatorFactory->getComparatorFor('abc', 'efg')->willReturn(null)->shouldBeCalledOnce();
        $this->assertFalse($this->getDefaultComparator($this->comparatorFactory->reveal())->isEqual('abc', 'efg'));
    }

    /**
     * @test
     *
     * @return void
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ComparisonFailure
     * @throws ExpectationFailedException
     */
    public function isEqualShouldReturnFalseWhenComparatorAssertEqualsThrowsComparisonFailure(): void
    {
        $this->comparatorFactory->getComparatorFor('a', 'b')->willReturn($this->comparator)->shouldBeCalledOnce();
        $this->comparator->assertEquals('a', 'b')->willThrow(ComparisonFailure::class)->shouldBeCalledOnce();
        $this->assertFalse($this->getDefaultComparator($this->comparatorFactory->reveal())->isEqual('a', 'b'));
    }

    /**
     * @test
     *
     * @return void
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ComparisonFailure
     * @throws ExpectationFailedException
     */
    public function isEqualShouldReturnTrueWhenComparatorAssertEqualsNotThrowsAnException(): void
    {
        $this->comparatorFactory->getComparatorFor('a', 'b')->willReturn($this->comparator)->shouldBeCalledOnce();
        $this->comparator->assertEquals('a', 'b')->willReturn(null)->shouldBeCalledOnce();
        $this->assertTrue($this->getDefaultComparator($this->comparatorFactory->reveal())->isEqual('a', 'b'));
    }

    /**
     * @test
     *
     * @return void
     * @throws RuntimeException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
     * @return void
     * @throws RuntimeException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function isEqualShouldReturnTrueWhenValuesAreEqual(): void
    {
        $this->assertTrue($this->getDefaultComparator()->isEqual('value1', 'value1'));
        $this->assertTrue($this->getDefaultComparator()->isEqual(['value1'], ['value1']));
        $this->assertTrue($this->getDefaultComparator()->isEqual(new stdClass(), new stdClass()));
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
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function isLessThanShouldReturnTrueWhenValueToCompareIsLessThanTheReferenceValue(): void
    {
        $this->assertTrue($this->getDefaultComparator()->isLessThan(0, -4));
        $this->assertTrue($this->getDefaultComparator()->isLessThan(1, 0));
        $this->assertTrue($this->getDefaultComparator()->isLessThan(2, 1));
        $this->assertTrue($this->getDefaultComparator()->isLessThan(10, 8));
        $this->assertTrue($this->getDefaultComparator()->isLessThan(true, false));
    }

    /**
     * @param ComparatorFactory|null $comparatorFactory
     * @return DefaultComparator
     */
    private function getDefaultComparator(?ComparatorFactory $comparatorFactory = null): DefaultComparator
    {
        return new DefaultComparator($comparatorFactory);
    }
}
