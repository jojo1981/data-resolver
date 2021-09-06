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

use Jojo1981\DataResolver\Extractor\CountExtractor;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
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
final class CountExtractorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|SequenceHandlerInterface */
    private ObjectProphecy $sequenceHandler;

    /** @var ObjectProphecy|Context */
    private ObjectProphecy $originalContext;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->sequenceHandler = $this->prophesize(SequenceHandlerInterface::class);
        $this->originalContext = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExtractorException
     * @throws HandlerException
     */
    public function extractShouldThrowAnExceptionBecauseSequenceHandlerDoesNotSupportTheDataFromContext(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledOnce();
        $this->originalContext->getPath()->willReturn('my-path')->shouldBeCalledOnce();
        $this->sequenceHandler->supports('my-data')->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(new ExtractorException('Could not extract data with `' . CountExtractor::class . '` at path: `my-path`'));

        $this->getCountExtractor()->extract($this->originalContext->reveal());
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ExtractorException
     * @throws HandlerException
     */
    public function extractShouldReturnTheResultFromTheSequenceHandlerFilterMethod(): void
    {
        $this->originalContext->getData()->willReturn('my-data')->shouldBeCalledTimes(2);
        $this->originalContext->getPath()->shouldNotBeCalled();
        $this->originalContext->copy()->shouldNotBeCalled();
        $this->sequenceHandler->supports('my-data')->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler->count('my-data')->willReturn(25)->shouldBeCalledOnce();

        $this->assertEquals(25, $this->getCountExtractor()->extract($this->originalContext->reveal()));
    }

    /**
     * @return CountExtractor
     * @throws ObjectProphecyException
     */
    private function getCountExtractor(): CountExtractor
    {
        return new CountExtractor($this->sequenceHandler->reveal());
    }
}
