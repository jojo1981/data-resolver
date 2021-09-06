<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
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
use SebastianBergmann\RecursionContext\InvalidArgumentException as SebastianBergmannInvalidArgumentException;
use tests\Jojo1981\DataResolver\Integration\AbstractIntegrationTestCase;

/**
 * @package tests\Jojo1981\DataResolver\Integration\Predicate
 */
final class CountPredicateTest extends AbstractIntegrationTestCase
{
    /**
     * @test
     * @coversNothing
     *
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     * @throws SebastianBergmannInvalidArgumentException
     * @throws ResolverException
     */
    public function checkHasCountPredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->hasCount(5);
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $this->assertFalse($predicate->match(new Context([1, 2, 3])));
        $this->assertFalse($predicate->match(new Context([])));
        $this->assertTrue($predicate->match(new Context(['a', 'b', 'c', 'd', 'e'])));
        $this->assertTrue($predicate->match(new Context([1, 2, 3, 4, 5])));
    }

    /**
     * @test
     * @coversNothing
     *
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     * @throws SebastianBergmannInvalidArgumentException
     * @throws ResolverException
     */
    public function checkHasNotCountPredicate(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->hasNotCount(5);
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $this->assertTrue($predicate->match(new Context([1, 2, 3])));
        $this->assertTrue($predicate->match(new Context([])));
        $this->assertFalse($predicate->match(new Context(['a', 'b', 'c', 'd', 'e'])));
        $this->assertFalse($predicate->match(new Context([1, 2, 3, 4, 5])));
    }
}
