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

use Jojo1981\DataResolver\Predicate\StringContainsPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
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
class StringContainsPredicateTest extends TestCase
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
     * @param string $substring
     * @param bool $caseSensitive
     * @param mixed $testData
     * @param bool $expectedResult
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnExpectedResult(
        string $substring,
        bool $caseSensitive,
        $testData,
        bool $expectedResult
    ): void
    {
        $this->context->getData()->willReturn($testData)->shouldBeCalledOnce();
        $this->assertEquals(
            $expectedResult,
            $this->getStringContainsPredicate($substring, $caseSensitive)->match($this->context->reveal())
        );
    }

    /**
     * @return array
     */
    public function getTestData(): array
    {
        return [
            ['text', true, true, false],
            ['text', true, false, false],
            ['text', true, null, false],
            ['text', true, new \stdClass(), false],
            ['text', true, [], false],
            ['text', true, [1, 2, 3], false],
            ['text', true, ['key' => 'value'], false],
            ['text', true, -4, false],
            ['text', true, 4, false],
            ['text', true, 2.67, false],
            ['text', true, -2.67, false],
            ['text', false, true, false],
            ['text', false, false, false],
            ['text', false, null, false],
            ['text', false, new \stdClass(), false],
            ['text', false, [], false],
            ['text', false, [1, 2, 3], false],
            ['text', false, ['key' => 'value'], false],
            ['text', false, -4, false],
            ['text', false, 4, false],
            ['text', false, 2.67, false],
            ['text', false, -2.67, false],
            ['text', false, 'other', false],
            ['text', true, 'other', false],
            ['text', false, 'ex', false],
            ['text', true, 'ex', false],
            ['ex', false, 'ex', true],
            ['ex', true, 'ex', true],
            ['ex', false, 'text', true],
            ['ex', true, 'text', true],
            ['ex', true, 'EX', false],
            ['EX', true, 'ex', false],
            ['ex', true, 'TEXT', false],
            ['EX', true, 'text', false]
        ];
    }

    /**
     * @param string $subString
     * @param bool $caseSensitive
     * @return StringContainsPredicate
     */
    private function getStringContainsPredicate(string $subString, bool $caseSensitive): StringContainsPredicate
    {
        return new StringContainsPredicate($subString, $caseSensitive);
    }
}