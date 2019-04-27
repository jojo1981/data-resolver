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
    public function matchShouldReturnFalseEvenWhenTheUsedIsEqualConstraintThrowsAnException(): void
    {
        /** @var ObjectProphecy|\SplObjectStorage $objectStorage1 */
        $objectStorage1 = $this->prophesize(\SplObjectStorage::class);

        /** @var ObjectProphecy|\SplObjectStorage $objectStorage2 */
        $objectStorage2 = $this->prophesize(\SplObjectStorage::class);
        $objectStorage2->rewind()->willThrow(new \Exception('Force the IsEqual constraint to throw an exception'));

        $this->assertFalse(
            (new EqualsPredicate($objectStorage1->reveal()))->match(new Context($objectStorage2->reveal()))
        );
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnFalseWhenValueDoesNotExistsInExpectedValues(): void
    {
        $this->assertFalse((new EqualsPredicate('value1'))->match(new Context('value2')));
        $this->assertFalse((new EqualsPredicate('Value1'))->match(new Context('value1')));
        $this->assertFalse((new EqualsPredicate(true))->match(new Context(false)));
        $this->assertFalse((new EqualsPredicate(false))->match(new Context(true)));
        $this->assertFalse((new EqualsPredicate(['name' => 'Tester']))->match(new Context(['name' => 'tester'])));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnTrueWhenValueDoesExistsInExpectedValues(): void
    {
        $this->assertTrue((new EqualsPredicate('value1'))->match(new Context('value1')));
        $this->assertTrue((new EqualsPredicate(['value1']))->match(new Context(['value1'])));
        $this->assertTrue((new EqualsPredicate(new \stdClass()))->match(new Context(new \stdClass())));
        $this->assertTrue((new EqualsPredicate(['name' => 'Tester']))->match(new Context(['name' => 'Tester'])));
        $this->assertTrue((new EqualsPredicate(true))->match(new Context(true)));
        $this->assertTrue((new EqualsPredicate(false))->match(new Context(false)));
        $this->assertTrue(
            (new EqualsPredicate(['name' => 'my-name', 'age' => 'my-age']))
                ->match(new Context(['age' => 'my-age', 'name' => 'my-name']))
        );
    }
}