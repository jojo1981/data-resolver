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

use Exception;
use Jojo1981\DataResolver\Extractor\HasPropertyExtractor;
use Jojo1981\DataResolver\Predicate\HasPropertyPredicate;
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
class HasPropertyPredicateTest extends TestCase
{
    /** @var ObjectProphecy|HasPropertyExtractor */
    private $hasPropertyExtractor;

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
        $this->hasPropertyExtractor = $this->prophesize(HasPropertyExtractor::class);
        $this->context = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function matchShouldReturnFalseWhenExtractorThrowsAnException(): void
    {
        $this->hasPropertyExtractor->extract($this->context)->willThrow(Exception::class)->shouldBeCalledOnce();
        $this->assertFalse($this->getHasPropertyPredicate()->match($this->context->reveal()));
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function matchShouldReturnFalseWhenWhenExtractorReturnsFalse(): void
    {
        $this->hasPropertyExtractor->extract($this->context)->willReturn(false)->shouldBeCalledOnce();
        $this->assertFalse($this->getHasPropertyPredicate()->match($this->context->reveal()));
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function matchShouldReturnTrueWhenWhenExtractorReturnsTrue(): void
    {
        $this->hasPropertyExtractor->extract($this->context)->willReturn(true)->shouldBeCalledOnce();
        $this->assertTrue($this->getHasPropertyPredicate()->match($this->context->reveal()));
    }

    /**
     * @return HasPropertyPredicate
     * @throws ObjectProphecyException
     */
    private function getHasPropertyPredicate(): HasPropertyPredicate
    {
        return new HasPropertyPredicate($this->hasPropertyExtractor->reveal());
    }
}
