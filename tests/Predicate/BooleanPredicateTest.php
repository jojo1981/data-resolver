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

use Jojo1981\DataResolver\Predicate\BooleanPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
final class BooleanPredicateTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<Context> */
    private ObjectProphecy $context;

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
     * @param mixed $value
     * @param array $expected
     * @return void
     * @throws ExpectationFailedException
     * @throws ObjectProphecyException
     */
    #[DataProvider('getTestData')]
    public function testMatchShouldReturnTheCorrectValueForIsTrue(mixed $value, array $expected): void
    {
        $this->context->getData()->willReturn($value)->shouldBeCalledOnce();

        self::assertEquals($expected['isTrue'], (new BooleanPredicate(true, true))->match($this->context->reveal()));
    }

    /**
     * @param mixed $value
     * @param array $expected
     * @return void
     * @throws ExpectationFailedException
     * @throws ObjectProphecyException
     */
    #[DataProvider('getTestData')]
    public function testMatchShouldReturnTheCorrectValueForIsTruly(mixed $value, array $expected): void
    {
        $this->context->getData()->willReturn($value)->shouldBeCalledOnce();

        self::assertEquals($expected['isTruly'], (new BooleanPredicate(true, false))->match($this->context->reveal()));
    }

    /**
     * @param mixed $value
     * @param array $expected
     * @return void
     * @throws ExpectationFailedException
     * @throws ObjectProphecyException
     */
    #[DataProvider('getTestData')]
    public function testMatchShouldReturnTheCorrectValueForIsFalse(mixed $value, array $expected): void
    {
        $this->context->getData()->willReturn($value)->shouldBeCalledOnce();

        self::assertEquals($expected['isFalse'], (new BooleanPredicate(false, true))->match($this->context->reveal()));
    }

    /**
     * @param mixed $value
     * @param array $expected
     * @return void
     * @throws ExpectationFailedException
     * @throws ObjectProphecyException
     */
    #[DataProvider('getTestData')]
    public function testMatchShouldReturnTheCorrectValueForIsFalsely(mixed $value, array $expected): void
    {
        $this->context->getData()->willReturn($value)->shouldBeCalledOnce();

        self::assertEquals(
            $expected['isFalsely'],
            (new BooleanPredicate(false, false))->match($this->context->reveal())
        );
    }

    /**
     * @return array[]
     */
    public static function getTestData(): array
    {
        return [
            [true, ['isTrue' => true, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            [false, ['isTrue' => false, 'isTruly' => false, 'isFalse' => true, 'isFalsely' => true]],
            [-1, ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            [-1.2, ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            [0, ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            [0.0, ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            [1, ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            [1.2, ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            [10, ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['', ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            ['0', ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            ['1', ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['text', ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['true', ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            ['false', ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            [null, ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            [
                new stdClass(),
                ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]
            ],
            [[], ['isTrue' => false, 'isTruly' => false, 'isFalse' => false, 'isFalsely' => true]],
            [[1, 2, 3], ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]],
            [
                ['zero', 'one', 'two'],
                ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]
            ],
            [
                [1 => 'one', 2 => 'two'],
                ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]
            ],
            [
                ['one' => 1, 'two' => 2, 'three' => 3],
                ['isTrue' => false, 'isTruly' => true, 'isFalse' => false, 'isFalsely' => false]
            ]
        ];
    }
}
