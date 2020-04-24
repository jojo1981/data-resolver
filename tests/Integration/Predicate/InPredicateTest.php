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
class InPredicateTest extends AbstractIntegrationTestCase
{
    /**
     * @test
     * @coversNothing
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws PHPUnitException
     * @throws ResolverException
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     */
    public function checkInPredicateWithStrings(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->in(['item1', 'item3']);
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $this->assertTrue($predicate->match(new Context('item1')));
        $this->assertFalse($predicate->match(new Context('item2')));
        $this->assertTrue($predicate->match(new Context('item3')));
    }

    /**
     * @test
     * @coversNothing
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ResolverException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     */
    public function checkNotInPredicateWithStrings(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->notIn(['item1', 'item3']);
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $this->assertFalse($predicate->match(new Context('item1')));
        $this->assertTrue($predicate->match(new Context('item2')));
        $this->assertFalse($predicate->match(new Context('item3')));
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
    public function checkInPredicateWithObjects(): void
    {
        $item1 = new stdClass();
        $item1->name = 'item1';

        $item3 = new stdClass();
        $item3->name = 'item3';

        $predicateBuilder = $this->getResolverBuilderFactory()->where()->in([$item1, $item3]);
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $this->assertTrue($predicate->match(new Context($item1)));
        $this->assertTrue($predicate->match(new Context($item3)));

        $item2 = new stdClass();
        $item2->name = 'item2';
        $this->assertFalse($predicate->match(new Context($item2)));

        $item4 = new stdClass();
        $item4->name = 'item1';
        $this->assertTrue($predicate->match(new Context($item4)));

        $item2->name = 'item3';
        $this->assertTrue($predicate->match(new Context($item2)));
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
    public function checkNotInPredicateWithObjects(): void
    {
        $item1 = new stdClass();
        $item1->name = 'item1';

        $item3 = new stdClass();
        $item3->name = 'item3';

        $predicateBuilder = $this->getResolverBuilderFactory()->where()->notIn([$item1, $item3]);
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $this->assertFalse($predicate->match(new Context($item1)));
        $this->assertFalse($predicate->match(new Context($item3)));

        $item2 = new stdClass();
        $item2->name = 'item2';
        $this->assertTrue($predicate->match(new Context($item2)));

        $item4 = new stdClass();
        $item4->name = 'item1';
        $this->assertFalse($predicate->match(new Context($item4)));

        $item2->name = 'item3';
        $this->assertFalse($predicate->match(new Context($item2)));
    }
}
