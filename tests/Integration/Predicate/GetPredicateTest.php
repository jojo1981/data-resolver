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

use Jojo1981\DataResolver\Exception\ResolverException;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\ExpectationFailedException;
use stdClass;
use tests\Jojo1981\DataResolver\Integration\AbstractIntegrationTestCase;

/**
 * @package tests\Jojo1981\DataResolver\Integration\Predicate
 */
final class GetPredicateTest extends AbstractIntegrationTestCase
{
    /**
     * @return void
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ResolverException
     * @throws ExtractorException
     */
    #[CoversNothing]
    public function testCheckGetPredicateUsingOneProperty(): void
    {
        $predicate1 = $this->getResolverBuilderFactory()->where('name')->equals('John Doe')->build();
        $predicate2 = $this->getResolverBuilderFactory()->where()->get('name')->equals('John Doe')->build();

        $testData = new stdClass();
        self::assertFalse($predicate1->match(new Context($testData)));
        self::assertFalse($predicate2->match(new Context($testData)));

        $testData->name = 'John Doe';
        self::assertTrue($predicate1->match(new Context($testData)));
        self::assertTrue($predicate2->match(new Context($testData)));
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ResolverException
     * @throws ExtractorException
     */
    #[CoversNothing]
    public function testCheckGetPredicateUsingMultipleProperties(): void
    {
        $predicate1 = $this->getResolverBuilderFactory()->where('level1.level2')->equals('level2')->build();
        $predicate2 = $this->getResolverBuilderFactory()->where('level1')->get('level2')->equals('level2')->build();
        $predicate3 = $this->getResolverBuilderFactory()->where()->get('level1')->get('level2')->equals('level2')->build();

        $testData = new stdClass();
        self::assertFalse($predicate1->match(new Context($testData)));
        self::assertFalse($predicate2->match(new Context($testData)));
        self::assertFalse($predicate3->match(new Context($testData)));

        $testData->level1 = new stdClass();
        self::assertFalse($predicate1->match(new Context($testData)));
        self::assertFalse($predicate2->match(new Context($testData)));
        self::assertFalse($predicate3->match(new Context($testData)));

        $testData->level1->level2 = 'nok';
        self::assertFalse($predicate1->match(new Context($testData)));
        self::assertFalse($predicate2->match(new Context($testData)));
        self::assertFalse($predicate3->match(new Context($testData)));

        $testData->level1->level2 = 'level2';
        self::assertTrue($predicate1->match(new Context($testData)));
        self::assertTrue($predicate2->match(new Context($testData)));
        self::assertTrue($predicate3->match(new Context($testData)));
    }
}
