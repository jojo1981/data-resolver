<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
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
use tests\Jojo1981\DataResolver\Integration\AbstractIntegrationTestCase;

/**
 * @package tests\Jojo1981\DataResolver\Integration\Predicate
 */
final class CountPredicateTest extends AbstractIntegrationTestCase
{
    /**
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     * @throws ResolverException
     */
    #[CoversNothing]
    public function testCheckHasCountPredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->hasCount(5);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        self::assertFalse($predicate->match(new Context([1, 2, 3])));
        self::assertFalse($predicate->match(new Context([])));
        self::assertTrue($predicate->match(new Context(['a', 'b', 'c', 'd', 'e'])));
        self::assertTrue($predicate->match(new Context([1, 2, 3, 4, 5])));
    }

    /**
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     * @throws ResolverException
     */
    #[CoversNothing]
    public function testCheckHasNotCountPredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->hasNotCount(5);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        self::assertTrue($predicate->match(new Context([1, 2, 3])));
        self::assertTrue($predicate->match(new Context([])));
        self::assertFalse($predicate->match(new Context(['a', 'b', 'c', 'd', 'e'])));
        self::assertFalse($predicate->match(new Context([1, 2, 3, 4, 5])));
    }
}
