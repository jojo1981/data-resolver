<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Predicate\NotPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use tests\Jojo1981\DataResolver\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
class NotPredicateTest extends TestCase
{
    /** @var ObjectProphecy|PredicateInterface */
    private $predicate;

    /** @var ObjectProphecy|Context */
    private $context;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->predicate = $this->prophesize(PredicateInterface::class);
        $this->context = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExtractorException
     */
    public function matchShouldReturnFalseWhenInjectedPredicateMatchReturnsTrue(): void
    {
        $this->predicate->match($this->context)->willReturn(true)->shouldBeCalled();
        $this->assertFalse($this->getNotPredicate()->match($this->context->reveal()));
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExtractorException
     */
    public function matchShouldReturnTrueWhenInjectedPredicateMatchReturnsFalse(): void
    {
        $this->predicate->match($this->context)->willReturn(false)->shouldBeCalled();
        $this->assertTrue($this->getNotPredicate()->match($this->context->reveal()));
    }

    /**
     * @return NotPredicate
     * @throws ObjectProphecyException
     */
    private function getNotPredicate(): NotPredicate
    {
        return new NotPredicate($this->predicate->reveal());
    }
}
