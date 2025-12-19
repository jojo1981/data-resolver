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

use Exception;
use Jojo1981\DataResolver\Extractor\HasPropertyExtractor;
use Jojo1981\DataResolver\Predicate\HasPropertyPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
final class HasPropertyPredicateTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<HasPropertyExtractor> */
    private ObjectProphecy $hasPropertyExtractor;

    /** @var ObjectProphecy<Context> */
    private ObjectProphecy $context;

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
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function testMatchShouldReturnFalseWhenExtractorThrowsAnException(): void
    {
        $this->hasPropertyExtractor->extract($this->context)->willThrow(Exception::class)->shouldBeCalledOnce();
        self::assertFalse($this->getHasPropertyPredicate()->match($this->context->reveal()));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function testMatchShouldReturnFalseWhenWhenExtractorReturnsFalse(): void
    {
        $this->hasPropertyExtractor->extract($this->context)->willReturn(false)->shouldBeCalledOnce();
        self::assertFalse($this->getHasPropertyPredicate()->match($this->context->reveal()));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function testMatchShouldReturnTrueWhenWhenExtractorReturnsTrue(): void
    {
        $this->hasPropertyExtractor->extract($this->context)->willReturn(true)->shouldBeCalledOnce();
        self::assertTrue($this->getHasPropertyPredicate()->match($this->context->reveal()));
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
