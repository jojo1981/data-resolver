<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver;

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
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
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver
 */
class ResolverTest extends TestCase
{
    /** @var ObjectProphecy|Context */
    private $context;

    /** @var ObjectProphecy|ExtractorInterface */
    private $extractor1;

    /** @var ObjectProphecy|ExtractorInterface */
    private $extractor2;

    /**
     * @throws DoubleException
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @return void
     */
    protected function setUp(): void
    {
        $this->context = $this->prophesize(Context::class);
        $this->extractor1 = $this->prophesize(ExtractorInterface::class);
        $this->extractor2 = $this->prophesize(ExtractorInterface::class);
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ExtractorException
     * @return void
     */
    public function resolverWithoutExtractorsShouldSimplyReturnTheDataWhichIsGivenToTheResolveMethod(): void
    {
        $resolver = new Resolver([]);
        $data = ['key' => 'value'];

        $this->assertEquals($data, $resolver->resolve($data));
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @return void
     */
    public function resolverWithoutExtractorsShouldSimplyReturnTheContextWhichIsGivenToTheResolveMethod(): void
    {
        $resolver = new Resolver([]);
        $this->context->setData(Argument::any())->shouldNotBeCalled();

        $this->assertSame($this->context->reveal(), $resolver->resolve($this->context->reveal()));
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @return void
     */
    public function resolverWithPassingContextToTheResolveMethodShouldUpdateContextAndPassItToTheExtractors(): void
    {
        $this->context->setData(['key1' => 'value1'])->willReturn($this->context)->shouldBeCalledOnce();
        $this->extractor1->extract($this->context)->willReturn(['key1' => 'value1'])->shouldBeCalledOnce();
        $this->extractor2->extract($this->context)->willReturn(['key2' => 'value2'])->shouldBeCalledOnce();

        $this->assertEquals(['key2' => 'value2'], $this->getResolver()->resolve($this->context->reveal()));
    }

    /**
     * @test
     *
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @return void
     */
    public function resolverWithPassingDataToTheResolveMethodShouldTransformItIntoAContextObjectAnPassItToTheExtractors(): void
    {
        $this->extractor1->extract(Argument::that(static function ($arg): bool {
            return (
                $arg instanceof Context
                && 'just-some-data' === $arg->getData()
                && '' === $arg->getPath()
                && $arg === $arg->pushPathPart('root')
            );
        }))->willReturn(['key1' => 'value1'])->shouldBeCalledOnce();
        $this->extractor2->extract(Argument::that(static function ($arg): bool {
            return (
                $arg instanceof Context
                && \array_key_exists('key1', $arg->getData())
                && 'value1' === $arg->getData()['key1']
                && 'root' === $arg->getPath()
            );

        }))->willReturn('last-result')->shouldBeCalledOnce();

        $this->assertEquals('last-result', $this->getResolver()->resolve('just-some-data'));
    }

    /**
     * @throws ObjectProphecyException
     * @return Resolver
     */
    private function getResolver(): Resolver
    {
        return new Resolver([$this->extractor1->reveal(), $this->extractor2->reveal()]);
    }
}