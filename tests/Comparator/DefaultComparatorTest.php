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
    public function isEqualShouldReturnFalseEvenWhenTheUsedIsEqualConstraintThrowsAnException(): void
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
    public function isEqualShouldReturnFalseWhenValueDoesNotExistsInExpectedValues(): void
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
    public function isEqualShouldReturnTrueWhenValueDoesExistsInExpectedValues(): void
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
     * @return DefaultComparator
     */
    private function getDefaultComparator(): DefaultComparator
    {
        return new DefaultComparator();
    }
}