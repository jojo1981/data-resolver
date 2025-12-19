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
use Prophecy\Exception\InvalidArgumentException as ProphecyInvalidArgumentException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use function array_key_exists;

/**
 * @package tests\Jojo1981\DataResolver
 */
final class ResolverTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<Context> */
    private ObjectProphecy $context;

    /** @var ObjectProphecy<ExtractorInterface> */
    private ObjectProphecy $extractor1;

    /** @var ObjectProphecy<ExtractorInterface> */
    private ObjectProphecy $extractor2;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->context = $this->prophesize(Context::class);
        $this->extractor1 = $this->prophesize(ExtractorInterface::class);
        $this->extractor2 = $this->prophesize(ExtractorInterface::class);
    }

    /**
     * @return void
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     * @throws HandlerException
     */
    public function testResolverWithoutExtractorsShouldSimplyReturnTheDataWhichIsGivenToTheResolveMethod(): void
    {
        $data = ['key' => 'value'];
        self::assertSame($data, (new Resolver([]))->resolve($data));
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     */
    public function testResolverWithoutExtractorsShouldSimplyReturnTheDataFromThePassedContext(): void
    {
        $data = ['key' => 'value'];
        $this->context->getData()->willReturn($data)->shouldBeCalledOnce();
        self::assertSame($data, (new Resolver([]))->resolve($this->context->reveal()));
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ExtractorException
     */
    public function testResolverWithPassingContextToTheResolveMethodShouldUpdateContextAndPassItToTheExtractors(): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->context->setData(['key1' => 'value1'])->willReturn($this->context)->shouldBeCalledOnce();
        $this->extractor1->extract($this->context)->willReturn(['key1' => 'value1'])->shouldBeCalledOnce();
        $this->extractor2->extract($this->context)->willReturn(['key2' => 'value2'])->shouldBeCalledOnce();

        self::assertEquals(['key2' => 'value2'], $this->getResolver()->resolve($this->context->reveal()));
    }

    /**
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ProphecyInvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testResolverWithPassingDataToTheResolveMethodShouldTransformItIntoAContextObjectAnPassItToTheExtractors(): void
    {
        /** @noinspection PhpParamsInspection */
        $this->extractor1->extract(Argument::that(static function ($arg): bool {
            return (
                $arg instanceof Context
                && 'just-some-data' === $arg->getData()
                && '' === $arg->getPath()
                && $arg === $arg->pushPathPart('root')
            );
        }))->willReturn(['key1' => 'value1'])->shouldBeCalledOnce();
        /** @noinspection PhpParamsInspection */
        $this->extractor2->extract(Argument::that(static function ($arg): bool {
            return (
                $arg instanceof Context
                && array_key_exists('key1', $arg->getData())
                && 'value1' === $arg->getData()['key1']
                && 'root' === $arg->getPath()
            );
        }))->willReturn('last-result')->shouldBeCalledOnce();

        self::assertEquals('last-result', $this->getResolver()->resolve('just-some-data'));
    }

    /**
     * @return Resolver
     * @throws ObjectProphecyException
     */
    private function getResolver(): Resolver
    {
        return new Resolver([$this->extractor1->reveal(), $this->extractor2->reveal()]);
    }
}
