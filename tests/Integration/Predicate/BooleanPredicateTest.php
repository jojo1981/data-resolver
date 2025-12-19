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

namespace tests\Jojo1981\DataResolver\Integration\Predicate;

use Jojo1981\DataResolver\Builder\Predicate\ConditionalPredicateBuilder;
use Jojo1981\DataResolver\Exception\ResolverException;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use stdClass;
use tests\Jojo1981\DataResolver\Integration\AbstractIntegrationTestCase;

/**
 * @package tests\Jojo1981\DataResolver\Integration\Predicate
 */
final class BooleanPredicateTest extends AbstractIntegrationTestCase
{
    /**
     * @return void
     * @throws ResolverException
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     */
    #[CoversNothing]
    public function testCheckIsTruePredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->isTrue();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        self::assertTrue($predicate->match(new Context(true)));

        self::assertFalse($predicate->match(new Context(false)));
        self::assertFalse($predicate->match(new Context(-1)));
        self::assertFalse($predicate->match(new Context(1)));
        self::assertFalse($predicate->match(new Context(-1.2)));
        self::assertFalse($predicate->match(new Context(1.2)));
        self::assertFalse($predicate->match(new Context(10)));
        self::assertFalse($predicate->match(new Context(new stdClass())));
        self::assertFalse($predicate->match(new Context('text')));
        self::assertFalse($predicate->match(new Context('true')));
        self::assertFalse($predicate->match(new Context('false')));
        self::assertFalse($predicate->match(new Context('1')));
        self::assertFalse($predicate->match(new Context([1, 2, 3])));
        self::assertFalse($predicate->match(new Context(['zero', 'one', 'two'])));
        self::assertFalse($predicate->match(new Context([1 => 'one', 2 => 'two'])));
        self::assertFalse($predicate->match(new Context(['one' => 1, 'two' => 2, 'three' => 3])));
        self::assertFalse($predicate->match(new Context(0)));
        self::assertFalse($predicate->match(new Context('')));
        self::assertFalse($predicate->match(new Context('0')));
        self::assertFalse($predicate->match(new Context(null)));
        self::assertFalse($predicate->match(new Context([])));
    }

    /**
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PHPUnitException
     * @throws PredicateException
     * @throws ResolverException
     * @throws ExpectationFailedException
     */
    #[CoversNothing]
    public function testCheckIsFalsePredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->isFalse();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        self::assertTrue($predicate->match(new Context(false)));

        self::assertFalse($predicate->match(new Context(true)));
        self::assertFalse($predicate->match(new Context(-1)));
        self::assertFalse($predicate->match(new Context(1)));
        self::assertFalse($predicate->match(new Context(-1.2)));
        self::assertFalse($predicate->match(new Context(1.2)));
        self::assertFalse($predicate->match(new Context(10)));
        self::assertFalse($predicate->match(new Context(new stdClass())));
        self::assertFalse($predicate->match(new Context('text')));
        self::assertFalse($predicate->match(new Context('true')));
        self::assertFalse($predicate->match(new Context('false')));
        self::assertFalse($predicate->match(new Context('1')));
        self::assertFalse($predicate->match(new Context([1, 2, 3])));
        self::assertFalse($predicate->match(new Context(['zero', 'one', 'two'])));
        self::assertFalse($predicate->match(new Context([1 => 'one', 2 => 'two'])));
        self::assertFalse($predicate->match(new Context(['one' => 1, 'two' => 2, 'three' => 3])));
        self::assertFalse($predicate->match(new Context(0)));
        self::assertFalse($predicate->match(new Context('')));
        self::assertFalse($predicate->match(new Context('0')));
        self::assertFalse($predicate->match(new Context(null)));
        self::assertFalse($predicate->match(new Context([])));
    }

    /**
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PHPUnitException
     * @throws PredicateException
     * @throws ResolverException
     * @throws ExpectationFailedException
     */
    #[CoversNothing]
    public function testCheckIsTrulyPredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->isTruly();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        self::assertTrue($predicate->match(new Context(true)));
        self::assertTrue($predicate->match(new Context(-1)));
        self::assertTrue($predicate->match(new Context(1)));
        self::assertTrue($predicate->match(new Context(-1.2)));
        self::assertTrue($predicate->match(new Context(1.2)));
        self::assertTrue($predicate->match(new Context(10)));
        self::assertTrue($predicate->match(new Context(new stdClass())));
        self::assertTrue($predicate->match(new Context('text')));
        self::assertTrue($predicate->match(new Context('true')));
        self::assertTrue($predicate->match(new Context('false')));
        self::assertTrue($predicate->match(new Context('1')));
        self::assertTrue($predicate->match(new Context([1, 2, 3])));
        self::assertTrue($predicate->match(new Context(['zero', 'one', 'two'])));
        self::assertTrue($predicate->match(new Context([1 => 'one', 2 => 'two'])));
        self::assertTrue($predicate->match(new Context(['one' => 1, 'two' => 2, 'three' => 3])));

        self::assertFalse($predicate->match(new Context(false)));
        self::assertFalse($predicate->match(new Context(0)));
        self::assertFalse($predicate->match(new Context('')));
        self::assertFalse($predicate->match(new Context('0')));
        self::assertFalse($predicate->match(new Context(null)));
        self::assertFalse($predicate->match(new Context([])));
    }

    /**
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PHPUnitException
     * @throws PredicateException
     * @throws ResolverException
     * @throws ExpectationFailedException
     */
    #[CoversNothing]
    public function testCheckIsFalselyPredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->isFalsely();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        self::assertTrue($predicate->match(new Context(false)));
        self::assertTrue($predicate->match(new Context(0)));
        self::assertTrue($predicate->match(new Context('')));
        self::assertTrue($predicate->match(new Context('0')));
        self::assertTrue($predicate->match(new Context(null)));
        self::assertTrue($predicate->match(new Context([])));

        self::assertFalse($predicate->match(new Context(true)));
        self::assertFalse($predicate->match(new Context(-1)));
        self::assertFalse($predicate->match(new Context(1)));
        self::assertFalse($predicate->match(new Context(-1.2)));
        self::assertFalse($predicate->match(new Context(1.2)));
        self::assertFalse($predicate->match(new Context(10)));
        self::assertFalse($predicate->match(new Context(new stdClass())));
        self::assertFalse($predicate->match(new Context('text')));
        self::assertFalse($predicate->match(new Context('true')));
        self::assertFalse($predicate->match(new Context('false')));
        self::assertFalse($predicate->match(new Context('1')));
        self::assertFalse($predicate->match(new Context([1, 2, 3])));
        self::assertFalse($predicate->match(new Context(['zero', 'one', 'two'])));
        self::assertFalse($predicate->match(new Context([1 => 'one', 2 => 'two'])));
        self::assertFalse($predicate->match(new Context(['one' => 1, 'two' => 2, 'three' => 3])));
    }
}
