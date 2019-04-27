<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Extractor;

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Extractor\ResolverExtractor;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Resolver;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Extractor
 */
class ResolverExtractorTest extends TestCase
{
    /** @var ObjectProphecy|Resolver */
    private $resolver;

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
        $this->resolver = $this->prophesize(Resolver::class);
        $this->context = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @return void
     */
    public function extractShouldReturnTheResultFromTheInjectedResolverResolveMethod(): void
    {
        $this->resolver->resolve($this->context)->willReturn('resolved-data')->shouldBeCalled();
        $this->assertEquals('resolved-data', $this->getResolverExtractor()->extract($this->context->reveal()));
    }

    /**
     * @throws ObjectProphecyException
     * @return ResolverExtractor
     */
    private function getResolverExtractor(): ResolverExtractor
    {
        return new ResolverExtractor($this->resolver->reveal());
    }
}