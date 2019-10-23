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

use Jojo1981\DataResolver\Predicate\NullPredicate;
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
class NullPredicateTest extends TestCase
{
    /** @var ObjectProphecy|Context */
    private $context;

    /**
     * @throws DoubleException
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @return void
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
     * @param bool $expected
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @return void
     */
    public function matchShouldReturnTheCorrectValueForIsTrue($value, bool $expected): void
    {
        $this->context->getData()->willReturn($value)->shouldBeCalledOnce();

        $this->assertEquals($expected, (new NullPredicate())->match($this->context->reveal()));
    }

    /**
     * @return array
     */
    public function getTestData(): array
    {
        return [
            [null, true],
            [true, false],
            [false, false],
            [-1, false],
            [-1.2, false],
            [0, false],
            [0.0, false],
            [1, false],
            [1.2, false],
            [10, false],
            ['', false],
            ['0', false],
            ['1', false],
            ['text', false],
            ['true', false],
            ['false', false],
            [new \stdClass(), false],
            [[], false],
            [[1, 2, 3], false],
            [['zero', 'one', 'two'], false],
            [[1 => 'one', 2 => 'two'], false],
            [['one' => 1, 'two' => 2, 'three' => 3], false]
        ];
    }
}