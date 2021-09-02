<?php declare(strict_types=1);
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
class HasPropertyPredicateTest extends AbstractIntegrationTestCase
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
    public function checkHasProperty(): void
    {
        $predicateBuilder = $this->getResolverBuilderFactory()->where()->hasProperty('name');
        $this->assertInstanceOf(ConditionalPredicateBuilder::class, $predicateBuilder);
        $predicate = $predicateBuilder->build();

        $data1 = [];
        $data2 = new stdClass();

        $this->assertFalse($predicate->match(new Context($data1)));
        $this->assertFalse($predicate->match(new Context($data2)));

        $data1['name'] = 'myName';
        $data2->name = 'myName';

        $this->assertTrue($predicate->match(new Context($data1)));
        $this->assertTrue($predicate->match(new Context($data2)));
    }
}
