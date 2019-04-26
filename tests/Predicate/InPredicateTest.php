<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Predicate\InPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
class InPredicateTest extends TestCase
{
    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnFalseWhenValueDoesNotExistsInExpectedValues(): void
    {
        $this->assertFalse((new InPredicate(['value1', 'value3']))->match(new Context('value2')));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnTrueWhenValueDoesExistsInExpectedValues(): void
    {
        $predicate = new InPredicate(['value1', 'value2', 'value3']);

        $this->assertTrue($predicate->match(new Context('value1')));
        $this->assertTrue($predicate->match(new Context('value2')));
        $this->assertTrue($predicate->match(new Context('value3')));
    }
}