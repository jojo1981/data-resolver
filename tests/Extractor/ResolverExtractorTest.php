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

namespace tests\Jojo1981\DataResolver\Extractor;

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Extractor\ResolverExtractor;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Resolver;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\InvalidArgumentException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @package tests\Jojo1981\DataResolver\Extractor
 */
final class ResolverExtractorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<Resolver> */
    private ObjectProphecy $resolver;

    /** @var ObjectProphecy<Context> */
    private ObjectProphecy $context;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws InvalidArgumentException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->resolver = $this->prophesize(Resolver::class);
        $this->context = $this->prophesize(Context::class);
        $this->context->setData(Argument::any())->shouldNotBeCalled();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->context->setPath(Argument::any())->shouldNotBeCalled();
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ObjectProphecyException
     * @throws ExtractorException
     */
    public function testExtractShouldReturnTheResultFromTheInjectedResolverResolveMethod(): void
    {
        $this->resolver->resolve($this->context)->willReturn('resolved-data')->shouldBeCalledOnce();
        self::assertEquals('resolved-data', $this->getResolverExtractor()->extract($this->context->reveal()));
    }

    /**
     * @return ResolverExtractor
     * @throws ObjectProphecyException
     */
    private function getResolverExtractor(): ResolverExtractor
    {
        return new ResolverExtractor($this->resolver->reveal());
    }
}
