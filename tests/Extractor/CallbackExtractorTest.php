<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Extractor;

use Jojo1981\DataResolver\Extractor\CallbackExtractor;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use tests\Jojo1981\DataResolver\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Extractor
 */
class CallbackExtractorTest extends TestCase
{
    /** @var ObjectProphecy|Context */
    private $originalContext;

    /** @var ObjectProphecy|Context */
    private $copiedContext;

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
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function extractShouldReturnTheResultFromTheCallback(): void
    {
        $this->originalContext->copy()->willReturn($this->copiedContext)->shouldBeCalled();
        $this->originalContext->getData()->shouldNotBeCalled();
        $this->copiedContext->getData()->willReturn('my-data')->shouldBeCalledOnce();

        $invokerCounter = 0;
        $callback = function (string $item) use (&$invokerCounter): string {
            $this->assertEquals('my-data', $item);
            $invokerCounter++;

            return 'newResult';
        };
        $this->assertEquals('newResult', (new CallbackExtractor($callback))->extract($this->originalContext->reveal()));
        $this->assertEquals(1, $invokerCounter);
    }
}
