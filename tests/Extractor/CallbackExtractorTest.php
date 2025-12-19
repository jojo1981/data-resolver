<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
declare(strict_types=1);

namespace tests\Jojo1981\DataResolver\Extractor;

use Jojo1981\DataResolver\Extractor\CallbackExtractor;
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
 * @package tests\Jojo1981\DataResolver\Extractor
 */
final class CallbackExtractorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<Context> */
    private ObjectProphecy $originalContext;

    /** @var ObjectProphecy<Context> */
    private ObjectProphecy $copiedContext;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->originalContext = $this->prophesize(Context::class);
        $this->copiedContext = $this->prophesize(Context::class);
    }


    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function testExtractShouldReturnTheResultFromTheCallback(): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalled();
        $this->originalContext->getData()->shouldNotBeCalled();
        $this->copiedContext->getData()->willReturn('my-data')->shouldBeCalledOnce();

        $invokerCounter = 0;
        $callback = function (string $item) use (&$invokerCounter): string {
            self::assertEquals('my-data', $item);
            $invokerCounter++;

            return 'newResult';
        };
        self::assertEquals('newResult', (new CallbackExtractor($callback))->extract($this->originalContext->reveal()));
        self::assertEquals(1, $invokerCounter);
    }
}
