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
final class InPredicateTest extends AbstractIntegrationTestCase
{
    /**
     * @return void
     * @throws PHPUnitException
     * @throws ResolverException
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     */
    #[CoversNothing]
    public function testCheckInPredicateWithStrings(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->in(['item1', 'item3']);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        self::assertTrue($predicate->match(new Context('item1')));
        self::assertFalse($predicate->match(new Context('item2')));
        self::assertTrue($predicate->match(new Context('item3')));
    }

    /**
     * @return void
     * @throws ResolverException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     */
    #[CoversNothing]
    public function testCheckNotInPredicateWithStrings(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->notIn(['item1', 'item3']);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        self::assertFalse($predicate->match(new Context('item1')));
        self::assertTrue($predicate->match(new Context('item2')));
        self::assertFalse($predicate->match(new Context('item3')));
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
    public function testCheckInPredicateWithObjects(): void
    {
        $item1 = new stdClass();
        $item1->name = 'item1';

        $item3 = new stdClass();
        $item3->name = 'item3';

        $predicateBuilder = $this->getResolverBuilderFactory()->where()->in([$item1, $item3]);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        self::assertTrue($predicate->match(new Context($item1)));
        self::assertTrue($predicate->match(new Context($item3)));

        $item2 = new stdClass();
        $item2->name = 'item2';
        self::assertFalse($predicate->match(new Context($item2)));

        $item4 = new stdClass();
        $item4->name = 'item1';
        self::assertTrue($predicate->match(new Context($item4)));

        $item2->name = 'item3';
        self::assertTrue($predicate->match(new Context($item2)));
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
    public function testCheckNotInPredicateWithObjects(): void
    {
        $item1 = new stdClass();
        $item1->name = 'item1';

        $item3 = new stdClass();
        $item3->name = 'item3';

        $predicateBuilder = $this->getResolverBuilderFactory()->where()->notIn([$item1, $item3]);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        self::assertFalse($predicate->match(new Context($item1)));
        self::assertFalse($predicate->match(new Context($item3)));

        $item2 = new stdClass();
        $item2->name = 'item2';
        self::assertTrue($predicate->match(new Context($item2)));

        $item4 = new stdClass();
        $item4->name = 'item1';
        self::assertFalse($predicate->match(new Context($item4)));

        $item2->name = 'item3';
        self::assertFalse($predicate->match(new Context($item2)));
    }
}
