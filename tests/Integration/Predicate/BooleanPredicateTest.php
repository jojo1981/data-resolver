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
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use stdClass;
use tests\Jojo1981\DataResolver\Integration\AbstractIntegrationTestCase;

/**
 * @package tests\Jojo1981\DataResolver\Integration\Predicate
 */
class BooleanPredicateTest extends AbstractIntegrationTestCase
{
    /**
     * @test
     * @coversNothing
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ResolverException
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     */
    public function checkIsTruePredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->isTrue();
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $this->assertTrue($predicate->match(new Context(true)));

        $this->assertFalse($predicate->match(new Context(false)));
        $this->assertFalse($predicate->match(new Context(-1)));
        $this->assertFalse($predicate->match(new Context(1)));
        $this->assertFalse($predicate->match(new Context(-1.2)));
        $this->assertFalse($predicate->match(new Context(1.2)));
        $this->assertFalse($predicate->match(new Context(10)));
        $this->assertFalse($predicate->match(new Context(new stdClass())));
        $this->assertFalse($predicate->match(new Context('text')));
        $this->assertFalse($predicate->match(new Context('true')));
        $this->assertFalse($predicate->match(new Context('false')));
        $this->assertFalse($predicate->match(new Context('1')));
        $this->assertFalse($predicate->match(new Context([1, 2, 3])));
        $this->assertFalse($predicate->match(new Context(['zero', 'one', 'two'])));
        $this->assertFalse($predicate->match(new Context([1 => 'one', 2 => 'two'])));
        $this->assertFalse($predicate->match(new Context(['one' => 1, 'two' => 2, 'three' => 3])));
        $this->assertFalse($predicate->match(new Context(0)));
        $this->assertFalse($predicate->match(new Context('')));
        $this->assertFalse($predicate->match(new Context('0')));
        $this->assertFalse($predicate->match(new Context(null)));
        $this->assertFalse($predicate->match(new Context([])));
    }

    /**
     * @test
     * @coversNothing
     *
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws PHPUnitException
     * @throws PredicateException
     * @throws ResolverException
     * @throws ExpectationFailedException
     */
    public function checkIsFalsePredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->isFalse();
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $this->assertTrue($predicate->match(new Context(false)));

        $this->assertFalse($predicate->match(new Context(true)));
        $this->assertFalse($predicate->match(new Context(-1)));
        $this->assertFalse($predicate->match(new Context(1)));
        $this->assertFalse($predicate->match(new Context(-1.2)));
        $this->assertFalse($predicate->match(new Context(1.2)));
        $this->assertFalse($predicate->match(new Context(10)));
        $this->assertFalse($predicate->match(new Context(new stdClass())));
        $this->assertFalse($predicate->match(new Context('text')));
        $this->assertFalse($predicate->match(new Context('true')));
        $this->assertFalse($predicate->match(new Context('false')));
        $this->assertFalse($predicate->match(new Context('1')));
        $this->assertFalse($predicate->match(new Context([1, 2, 3])));
        $this->assertFalse($predicate->match(new Context(['zero', 'one', 'two'])));
        $this->assertFalse($predicate->match(new Context([1 => 'one', 2 => 'two'])));
        $this->assertFalse($predicate->match(new Context(['one' => 1, 'two' => 2, 'three' => 3])));
        $this->assertFalse($predicate->match(new Context(0)));
        $this->assertFalse($predicate->match(new Context('')));
        $this->assertFalse($predicate->match(new Context('0')));
        $this->assertFalse($predicate->match(new Context(null)));
        $this->assertFalse($predicate->match(new Context([])));
    }

    /**
     * @test
     * @coversNothing
     *
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws PHPUnitException
     * @throws PredicateException
     * @throws ResolverException
     * @throws ExpectationFailedException
     */
    public function checkIsTrulyPredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->isTruly();
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $this->assertTrue($predicate->match(new Context(true)));
        $this->assertTrue($predicate->match(new Context(-1)));
        $this->assertTrue($predicate->match(new Context(1)));
        $this->assertTrue($predicate->match(new Context(-1.2)));
        $this->assertTrue($predicate->match(new Context(1.2)));
        $this->assertTrue($predicate->match(new Context(10)));
        $this->assertTrue($predicate->match(new Context(new stdClass())));
        $this->assertTrue($predicate->match(new Context('text')));
        $this->assertTrue($predicate->match(new Context('true')));
        $this->assertTrue($predicate->match(new Context('false')));
        $this->assertTrue($predicate->match(new Context('1')));
        $this->assertTrue($predicate->match(new Context([1, 2, 3])));
        $this->assertTrue($predicate->match(new Context(['zero', 'one', 'two'])));
        $this->assertTrue($predicate->match(new Context([1 => 'one', 2 => 'two'])));
        $this->assertTrue($predicate->match(new Context(['one' => 1, 'two' => 2, 'three' => 3])));

        $this->assertFalse($predicate->match(new Context(false)));
        $this->assertFalse($predicate->match(new Context(0)));
        $this->assertFalse($predicate->match(new Context('')));
        $this->assertFalse($predicate->match(new Context('0')));
        $this->assertFalse($predicate->match(new Context(null)));
        $this->assertFalse($predicate->match(new Context([])));
    }

    /**
     * @test
     * @coversNothing
     *
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws PHPUnitException
     * @throws PredicateException
     * @throws ResolverException
     * @throws ExpectationFailedException
     */
    public function checkIsFalselyPredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->isFalsely();
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $this->assertTrue($predicate->match(new Context(false)));
        $this->assertTrue($predicate->match(new Context(0)));
        $this->assertTrue($predicate->match(new Context('')));
        $this->assertTrue($predicate->match(new Context('0')));
        $this->assertTrue($predicate->match(new Context(null)));
        $this->assertTrue($predicate->match(new Context([])));

        $this->assertFalse($predicate->match(new Context(true)));
        $this->assertFalse($predicate->match(new Context(-1)));
        $this->assertFalse($predicate->match(new Context(1)));
        $this->assertFalse($predicate->match(new Context(-1.2)));
        $this->assertFalse($predicate->match(new Context(1.2)));
        $this->assertFalse($predicate->match(new Context(10)));
        $this->assertFalse($predicate->match(new Context(new stdClass())));
        $this->assertFalse($predicate->match(new Context('text')));
        $this->assertFalse($predicate->match(new Context('true')));
        $this->assertFalse($predicate->match(new Context('false')));
        $this->assertFalse($predicate->match(new Context('1')));
        $this->assertFalse($predicate->match(new Context([1, 2, 3])));
        $this->assertFalse($predicate->match(new Context(['zero', 'one', 'two'])));
        $this->assertFalse($predicate->match(new Context([1 => 'one', 2 => 'two'])));
        $this->assertFalse($predicate->match(new Context(['one' => 1, 'two' => 2, 'three' => 3])));
    }
}
