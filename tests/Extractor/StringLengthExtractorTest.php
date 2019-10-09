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
use Jojo1981\DataResolver\Extractor\StringLengthExtractor;
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
class StringLengthExtractorTest extends TestCase
{
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
        $this->context = $this->prophesize(Context::class);
    }

    /**
     * @test
     *
     * @throws ObjectProphecyException
     * @throws ExtractorException
     * @return void
     */
    public function extractShouldThrowAnExceptionBecauseOnlyStringDataIsSupportedTest1(): void
    {
        $this->context->getData()->willReturn(20)->shouldBeCalledOnce();
        $this->context->getPath()->willReturn('my-path')->shouldBeCalledOnce();

        $this->expectExceptionObject(new ExtractorException(
            'Could not extract data with `' . StringLengthExtractor::class . '` at path: `my-path`. ' .
            'Data is not of type string, but of type: integer'
        ));

        $this->getStringLengthExtractor()->extract($this->context->reveal());
    }

    /**
     * @test
     *
     * @throws ObjectProphecyException
     * @throws ExtractorException
     * @return void
     */
    public function extractShouldThrowAnExceptionBecauseOnlyStringDataIsSupportedTest2(): void
    {
        $this->context->getData()->willReturn(new \stdClass())->shouldBeCalledOnce();
        $this->context->getPath()->willReturn('my-path')->shouldBeCalledOnce();

        $this->expectExceptionObject(new ExtractorException(
            'Could not extract data with `' . StringLengthExtractor::class . '` at path: `my-path`. ' .
            'Data is not of type string, but of type: stdClass'
        ));

        $this->getStringLengthExtractor()->extract($this->context->reveal());
    }

    /**
     * @test
     *
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ExtractorException
     * @return void
     */
    public function extractShouldReturnTheStringLength(): void
    {
        $this->context->getData()->willReturn('', 'text', 'a', 'not')->shouldBeCalledTimes(4);
        $this->context->getPath()->shouldNotBeCalled();

        $context = $this->context->reveal();
        $this->assertEquals(0, $this->getStringLengthExtractor()->extract($context));
        $this->assertEquals(4, $this->getStringLengthExtractor()->extract($context));
        $this->assertEquals(1, $this->getStringLengthExtractor()->extract($context));
        $this->assertEquals(3, $this->getStringLengthExtractor()->extract($context));
    }

    /**
     * @return StringLengthExtractor
     */
    private function getStringLengthExtractor(): StringLengthExtractor
    {
        return new StringLengthExtractor();
    }
}