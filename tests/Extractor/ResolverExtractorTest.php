<?php declare(strict_types=1);
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
use Prophecy\Argument;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Extractor
 */
final class ResolverExtractorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|Resolver */
    private ObjectProphecy $resolver;

    /** @var ObjectProphecy|Context */
    private ObjectProphecy $context;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->resolver = $this->prophesize(Resolver::class);
        $this->context = $this->prophesize(Context::class);
        $this->context->setData(Argument::any())->shouldNotBeCalled();
        $this->context->setPath(Argument::any())->shouldNotBeCalled();
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
    public function extractShouldReturnTheResultFromTheInjectedResolverResolveMethod(): void
    {
        $this->resolver->resolve($this->context)->willReturn('resolved-data')->shouldBeCalledOnce();
        $this->assertEquals('resolved-data', $this->getResolverExtractor()->extract($this->context->reveal()));
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
