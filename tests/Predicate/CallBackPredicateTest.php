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

namespace tests\Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Predicate\CallBackPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
final class CallBackPredicateTest extends TestCase
{
    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testMatchShouldReturnFalseBecauseCallbackWillBeCalledAndReturnFalse(): void
    {
        $called = false;
        $callback = $this->buildCallback($called, false);

        self::assertFalse((new CallBackPredicate($callback))->match(new Context('my-data')));
        self::assertTrue($called, 'Expect callback to be called once, not called at all');
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testMatchShouldReturnTrueBecauseCallbackWillBeCalledAndReturnTrue(): void
    {
        $called = false;
        $callback = $this->buildCallback($called, true);

        self::assertTrue((new CallBackPredicate($callback))->match(new Context('my-data')));
        self::assertTrue($called, 'Expect callback to be called once, not called at all');
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testMatchShouldReturnFalseBecauseValueReturnedByCallbackIsEvaluatedToFalse(): void
    {
        $called = false;
        $callback = $this->buildCallback($called, '');

        self::assertFalse((new CallBackPredicate($callback))->match(new Context('my-data')));
        self::assertTrue($called, 'Expect callback to be called once, not called at all');

        $called = false;
        $callback = $this->buildCallback($called, null);

        self::assertFalse((new CallBackPredicate($callback))->match(new Context('my-data')));
        self::assertTrue($called, 'Expect callback to be called once, not called at all');
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testMatchShouldReturnTrueBecauseValueReturnedByCallbackIsEvaluatedToTrue(): void
    {
        $called = false;
        $callback = $this->buildCallback($called, 'yes');

        self::assertTrue((new CallBackPredicate($callback))->match(new Context('my-data')));
        self::assertTrue($called, 'Expect callback to be called once, not called at all');
    }

    /**
     * @param bool $called
     * @param mixed $returnValue
     * @return callable
     */
    private function buildCallback(bool &$called, mixed $returnValue): callable
    {
        return function ($value) use (&$called, $returnValue) {
            $expectedValue = 'my-data';
            if (true === $called) {
                $this->fail('Expect callback to only be called once');
            }
            $called = true;
            self::assertEquals($expectedValue, $value);

            return $returnValue;
        };
    }
}
