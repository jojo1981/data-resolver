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

use Jojo1981\DataResolver\Predicate\BooleanPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use stdClass;
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
class BooleanPredicateTest extends TestCase
{
    /** @var ObjectProphecy|Context */
    private $context;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->context = $this->prophesize(Context::class);
    }

    /**
     * @test
     * @dataProvider getTestData
     *
     * @param mixed $value
     * @param array $expected
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     */
    public function matchShouldReturnTheCorrectValueForIsTrue($value, array $expected): void
    {
        $this->context->getData()->willReturn($value)->shouldBeCalledOnce();

        $this->assertEquals($expected['isTrue'], (new BooleanPredicate(true, true))->match($this->context->reveal()));
    }

    /**
     * @test
     * @dataProvider getTestData
     *
     * @param mixed $value
     * @param array $expected
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     */
    public function matchShouldReturnTheCorrectValueForIsTruly($value, array $expected): void
    {
        $this->context->getData()->willReturn($value)->shouldBeCalledOnce();

        $this->assertEquals($expected['isTruly'], (new BooleanPredicate(true, false))->match($this->context->reveal()));
    }

    /**
     * @test
     * @dataProvider getTestData
     *
     * @param mixed $value
     * @param array $expected
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     */
    public function matchShouldReturnTheCorrectValueForIsFalse($value, array $expected): void
    {
        $this->context->getData()->willReturn($value)->shouldBeCalledOnce();

        $this->assertEquals($expected['isFalse'], (new BooleanPredicate(false, true))->match($this->context->reveal()));
    }

    /**
     * @test
     * @dataProvider getTestData
     *
     * @param mixed $value
     * @param array $expected
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     */
    public function matchShouldReturnTheCorrectValueForIsFalsely($value, array $expected): void
    {
        $this->context->getData()->willReturn($value)->shouldBeCalledOnce();

        $this->assertEquals(
            $expected['isFalsely'],
            (new BooleanPredicate(false, false))->match($this->context->reveal())
        );
    }

    /**
     * @return array[]
     */
    public function getTestData(): array
    {
        return [
            ['value' => true, ['isTrue' => true, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['value' => false, ['isTrue' => false, 'isTruly' => false, 'isFalse' => true, 'isFalsely' => true]],
            ['value' => -1, ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['value' => -1.2, ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['value' => 0, ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            ['value' => 0.0, ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            ['value' => 1, ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['value' => 1.2, ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['value' => 10, ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['value' => '', ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            ['value' => '0', ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            ['value' => '1', ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['value' => 'text', ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['value' => 'true', ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['value' => 'false', ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['value' => null, ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            [
                'value' => new stdClass(),
                ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]
            ],
            ['value' => [], ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            ['value' => [1, 2, 3], ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            [
                'value' => ['zero', 'one', 'two'],
                ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]
            ],
            [
                'value' => [1 => 'one', 2 => 'two'],
                ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]
            ],
            [
                'value' => ['one' => 1, 'two' => 2, 'three' => 3],
                ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]
            ]
        ];
    }
}
