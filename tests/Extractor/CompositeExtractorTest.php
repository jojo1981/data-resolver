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

use Jojo1981\DataResolver\Extractor\CompositeExtractor;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
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
final class CompositeExtractorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ExtractorInterface */
    private ObjectProphecy $extractor1;

    /** @var ObjectProphecy|ExtractorInterface */
    private ObjectProphecy $extractor2;

    /** @var ObjectProphecy|Context */
    private ObjectProphecy $originalContext;

    /** @var ObjectProphecy|Context */
    private ObjectProphecy $copiedContext;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->extractor1 = $this->prophesize(ExtractorInterface::class);
        $this->extractor2 = $this->prophesize(ExtractorInterface::class);
        $this->originalContext = $this->prophesize(Context::class);
        $this->copiedContext = $this->prophesize(Context::class);
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
    public function extractShouldReturnTheResultOfExtractor2WhichGetTheResultOfExtractor1(): void
    {
        $this->extractor1->extract($this->originalContext)->willReturn('Result1')->shouldBeCalledOnce();
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->copiedContext->setData('Result1')->willReturn($this->copiedContext)->shouldBeCalledOnce();
        $this->extractor2->extract($this->copiedContext)->willReturn('Result2')->shouldBeCalledOnce();

        $this->assertEquals('Result2', $this->getCompositeExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @return CompositeExtractor
     * @throws ObjectProphecyException
     */
    private function getCompositeExtractor(): CompositeExtractor
    {
        return new CompositeExtractor($this->extractor1->reveal(), $this->extractor2->reveal());
    }
}
