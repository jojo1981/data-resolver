<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Integration\Predicate;

use Jojo1981\DataResolver\Builder\Predicate\ConditionalPredicateBuilder;
use Jojo1981\DataResolver\Exception\ResolverException;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use tests\Jojo1981\DataResolver\Integration\AbstractIntegrationTestCase;

/**
 * @package tests\Jojo1981\DataResolver\Integration\Predicate
 */
class CallbackPredicateTest extends AbstractIntegrationTestCase
{
    /**
     * @test
     * @coversNothing
     * @dataProvider getTestData
     *
     * @param mixed $testValue
     * @param bool $expected
     * @throws ExpectationFailedException
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws PredicateException
     * @throws ResolverException
     * @return void
     */
    public function checkCallbackPredicate($testValue, bool $expected): void
    {
        $calledTimes = 0;
        $callback = function ($arg) use (&$calledTimes, $testValue) {
            $calledTimes++;
            $this->assertEquals($testValue, $arg);

            return $arg;
        };
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->callback($callback);
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $this->assertEquals($expected, $predicate->match(new Context($testValue)));
        $this->assertEquals(1, $calledTimes);
    }

    /**
     * @return array[]
     */
    public function getTestData(): array
    {
        return [
            [true, true],
            [-1, true],
            [1, true],
            [-1.2, true],
            [1.2, true],
            [10, true],
            [new \stdClass(), true],
            ['text', true],
            ['true', true],
            ['false', true],
            ['1', true],
            [[1, 2, 3], true],
            [['zero', 'one', 'two'], true],
            [[1 => 'one', 2 => 'two'], true],
            [['one' => 1, 'two' => 2, 'three' => 3], true],
            [false, false],
            [0, false],
            ['', false],
            ['0', false],
            [[], false],
            [null, false]
        ];
    }
}