<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Predicate\StringRegexPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use stdClass;
use tests\Jojo1981\DataResolver\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
class StringRegexPredicateTest extends TestCase
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
     * @param string $pattern
     * @param mixed $testData
     * @param bool $expectedResult
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     */
    public function matchShouldReturnExpectedResult(
        string $pattern,
        $testData,
        bool $expectedResult
    ): void {
        $this->context->getData()->willReturn($testData)->shouldBeCalledOnce();
        $this->assertEquals(
            $expectedResult,
            $this->getStringRegexPredicate($pattern)->match($this->context->reveal())
        );
    }

    /**
     * @return array[]
     */
    public function getTestData(): array
    {
        return [
            ['/text/', true, false],
            ['/text/', false, false],
            ['/text/', null, false],
            ['/text/', new stdClass(), false],
            ['/text/', [], false],
            ['/text/', [1, 2, 3], false],
            ['/text/', ['key' => 'value'], false],
            ['/text/', -4, false],
            ['/text/', 4, false],
            ['/text/', 2.67, false],
            ['/text/', -2.67, false],
            ['/text/', true, false],
            ['/text/', false, false],
            ['/text/', null, false],
            ['/text/', new stdClass(), false],
            ['/text/', [], false],
            ['/text/', [1, 2, 3], false],
            ['/text/', ['key' => 'value'], false],
            ['/text/', -4, false],
            ['/text/', 4, false],
            ['/text/', 2.67, false],
            ['/text/', -2.67, false],
            ['/text/', 'other', false],
            ['/text/', 'text', true],
            ['/text/', 'Text', false],
            ['/text/i', 'Text', true],
            ['/^text.*/', 'text from my', true],
            ['/^te?xt$/', 'text', true],
            ['/^te?xt$/', 'txt', true],
            ['/^te?xt$/', 'my txt', false],
            ['/^te?xt$/', 'text 1', false]
        ];
    }

    /**
     * @param string $pattern
     * @return StringRegexPredicate
     */
    private function getStringRegexPredicate(string $pattern): StringRegexPredicate
    {
        return new StringRegexPredicate($pattern);
    }
}
