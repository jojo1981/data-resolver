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

namespace tests\Jojo1981\DataResolver\Comparator;

use Jojo1981\DataResolver\Comparator\DefaultComparator;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;
use SebastianBergmann\Comparator\RuntimeException;
use stdClass;

/**
 * @package tests\Jojo1981\DataResolver\Comparator
 */
final class DefaultComparatorTest extends TestCase
{
    /**
     * @return void
     * @throws RuntimeException
     * @throws ExpectationFailedException
     */
    public function testIsEqualShouldReturnFalseWhenComparatorFactoryHasNoComparatorFor(): void
    {
        self::assertFalse($this->getDefaultComparator()->isEqual('abc', 'efg'));
    }

    /**
     * @return void
     * @throws RuntimeException
     * @throws ExpectationFailedException
     */
    public function testIsEqualShouldReturnFalseWhenComparatorAssertEqualsThrowsComparisonFailure(): void
    {
        self::assertFalse($this->getDefaultComparator()->isEqual('a', 'b'));
    }

    /**
     * @return void
     * @throws RuntimeException
     * @throws ExpectationFailedException
     */
    public function testIsEqualShouldReturnFalseWhenComparatorAssertEqualsNotThrowsAnException(): void
    {
        self::assertFalse($this->getDefaultComparator()->isEqual('a', 'b'));
    }

    /**
     * @return void
     * @throws RuntimeException
     * @throws ExpectationFailedException
     */
    public function testIsEqualShouldReturnFalseWhenValuesAreNotEqual(): void
    {
        self::assertFalse($this->getDefaultComparator()->isEqual('value1', 'value2'));
        self::assertFalse($this->getDefaultComparator()->isEqual('Value1', 'value1'));
        self::assertFalse($this->getDefaultComparator()->isEqual(true, false));
        self::assertFalse($this->getDefaultComparator()->isEqual(false, true));
        self::assertFalse($this->getDefaultComparator()->isEqual(['name' => 'Tester'], ['name' => 'tester']));
    }

    /**
     * @return void
     * @throws RuntimeException
     * @throws ExpectationFailedException
     */
    public function testIsEqualShouldReturnTrueWhenValuesAreEqual(): void
    {
        self::assertTrue($this->getDefaultComparator()->isEqual('value1', 'value1'));
        self::assertTrue($this->getDefaultComparator()->isEqual(['value1'], ['value1']));
        self::assertTrue($this->getDefaultComparator()->isEqual(new stdClass(), new stdClass()));
        self::assertTrue($this->getDefaultComparator()->isEqual(['name' => 'Tester'], ['name' => 'Tester']));
        self::assertTrue($this->getDefaultComparator()->isEqual(true, true));
        self::assertTrue($this->getDefaultComparator()->isEqual(false, false));
        self::assertTrue($this->getDefaultComparator()->isEqual(
            ['name' => 'my-name', 'age' => 'my-age'],
            ['age' => 'my-age', 'name' => 'my-name']
        ));
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testIsGreaterThanShouldReturnFalseWhenValueToCompareIsNotGreaterThanTheReferenceValue(): void
    {
        self::assertFalse($this->getDefaultComparator()->isGreaterThan(-4, -4));
        self::assertFalse($this->getDefaultComparator()->isGreaterThan(0, 0));
        self::assertFalse($this->getDefaultComparator()->isGreaterThan(1, 1));
        self::assertFalse($this->getDefaultComparator()->isGreaterThan(10, 8));
        self::assertFalse($this->getDefaultComparator()->isGreaterThan(false, false));
        self::assertFalse($this->getDefaultComparator()->isGreaterThan(true, false));
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testIsGreaterThanShouldReturnTrueWhenValueToCompareIsGreaterThanTheReferenceValue(): void
    {
        self::assertTrue($this->getDefaultComparator()->isGreaterThan(-4, 0));
        self::assertTrue($this->getDefaultComparator()->isGreaterThan(0, 1));
        self::assertTrue($this->getDefaultComparator()->isGreaterThan(1, 2));
        self::assertTrue($this->getDefaultComparator()->isGreaterThan(8, 10));
        self::assertTrue($this->getDefaultComparator()->isGreaterThan(false, true));
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testIsLessThanShouldReturnFalseWhenValueToCompareIsNotLessThanTheReferenceValue(): void
    {
        self::assertFalse($this->getDefaultComparator()->isLessThan(-4, -4));
        self::assertFalse($this->getDefaultComparator()->isLessThan(0, 0));
        self::assertFalse($this->getDefaultComparator()->isLessThan(1, 1));
        self::assertFalse($this->getDefaultComparator()->isLessThan(8, 10));
        self::assertFalse($this->getDefaultComparator()->isLessThan(false, false));
        self::assertFalse($this->getDefaultComparator()->isLessThan(false, true));
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testIsLessThanShouldReturnTrueWhenValueToCompareIsLessThanTheReferenceValue(): void
    {
        self::assertTrue($this->getDefaultComparator()->isLessThan(0, -4));
        self::assertTrue($this->getDefaultComparator()->isLessThan(1, 0));
        self::assertTrue($this->getDefaultComparator()->isLessThan(2, 1));
        self::assertTrue($this->getDefaultComparator()->isLessThan(10, 8));
        self::assertTrue($this->getDefaultComparator()->isLessThan(true, false));
    }

    /**
     * @return DefaultComparator
     */
    private function getDefaultComparator(): DefaultComparator
    {
        return new DefaultComparator(ComparatorFactory::getInstance());
    }
}
