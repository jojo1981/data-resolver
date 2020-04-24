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

use Jojo1981\DataResolver\Extractor\HasPropertyExtractor;
use Jojo1981\DataResolver\Predicate\HasPropertyPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use tests\Jojo1981\DataResolver\TestCase;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
class HasPropertyPredicateTest extends TestCase
{
    /** @var ObjectProphecy|HasPropertyExtractor */
    private $hasPropertyExtractor;

    /** @var ObjectProphecy|Context */
    private $context;

    /**
     * @throws DoubleException
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @return void
     */
    protected function setUp(): void
    {
        $this->hasPropertyExtractor = $this->prophesize(HasPropertyExtractor::class);
        $this->context = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @return void
     */
    public function matchShouldReturnFalseWhenExtractorThrowsAnException(): void
    {
        $this->hasPropertyExtractor->extract($this->context)->willThrow(\Exception::class)->shouldBeCalledOnce();
        $this->assertFalse($this->getHasPropertyPredicate()->match($this->context->reveal()));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnFalseWhenWhenExtractorReturnsFalse(): void
    {
        $this->hasPropertyExtractor->extract($this->context)->willReturn(false)->shouldBeCalledOnce();
        $this->assertFalse($this->getHasPropertyPredicate('propertyName')->match($this->context->reveal()));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @return void
     */
    public function matchShouldReturnTrueWhenWhenExtractorReturnsTrue(): void
    {
        $this->hasPropertyExtractor->extract($this->context)->willReturn(true)->shouldBeCalledOnce();
        $this->assertTrue($this->getHasPropertyPredicate('propertyName')->match($this->context->reveal()));
    }

    /**
     * @throws ObjectProphecyException
     * @return HasPropertyPredicate
     */
    private function getHasPropertyPredicate(): HasPropertyPredicate
    {
        return new HasPropertyPredicate($this->hasPropertyExtractor->reveal());
    }
}