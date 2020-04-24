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

use Jojo1981\DataResolver\Predicate\CallBackPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use tests\Jojo1981\DataResolver\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
class CallBackPredicateTest extends TestCase
{
    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnFalseBecauseCallbackWillBeCalledAndReturnFalse(): void
    {
        $called = false;
        $callback = $this->buildCallback($called, 'my-data', false);

        $this->assertFalse((new CallBackPredicate($callback))->match(new Context('my-data')));
        $this->assertTrue($called, 'Expect callback to be called once, not called at all');
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnTrueBecauseCallbackWillBeCalledAndReturnTrue(): void
    {
        $called = false;
        $callback = $this->buildCallback($called, 'my-data', true);

        $this->assertTrue((new CallBackPredicate($callback))->match(new Context('my-data')));
        $this->assertTrue($called, 'Expect callback to be called once, not called at all');
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnFalseBecauseValueReturnedByCallbackIsEvaluatedToFalse(): void
    {
        $called = false;
        $callback = $this->buildCallback($called, 'my-data', '');

        $this->assertFalse((new CallBackPredicate($callback))->match(new Context('my-data')));
        $this->assertTrue($called, 'Expect callback to be called once, not called at all');

        $called = false;
        $callback = $this->buildCallback($called, 'my-data', null);

        $this->assertFalse((new CallBackPredicate($callback))->match(new Context('my-data')));
        $this->assertTrue($called, 'Expect callback to be called once, not called at all');
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnTrueBecauseValueReturnedByCallbackIsEvaluatedToTrue(): void
    {
        $called = false;
        $callback = $this->buildCallback($called, 'my-data', 'yes');

        $this->assertTrue((new CallBackPredicate($callback))->match(new Context('my-data')));
        $this->assertTrue($called, 'Expect callback to be called once, not called at all');
    }

    /**
     * @param bool $called
     * @param $expectedValue
     * @param $returnValue
     * @return callable
     */
    private function buildCallback(bool &$called, $expectedValue, $returnValue): callable
    {
        return function ($value) use (&$called, $expectedValue, $returnValue) {
            if (true === $called) {
                $this->fail('Expect callback to only be called once');
            }
            $called = true;
            $this->assertEquals($expectedValue, $value);

            return $returnValue;
        };
    }
}