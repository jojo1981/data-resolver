<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Handler\SequenceHandler;

use ArrayIterator;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandler\CompositeSequenceHandler;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use PHPUnit\Framework\ExpectationFailedException;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use stdClass;
use tests\Jojo1981\DataResolver\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Handler\SequenceHandler
 */
class CompositeSequenceHandlerTest extends TestCase
{
    /** @var ObjectProphecy|SequenceHandlerInterface */
    private $sequenceHandler1;

    /** @var ObjectProphecy|SequenceHandlerInterface */
    private $sequenceHandler2;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->sequenceHandler1 = $this->prophesize(SequenceHandlerInterface::class);
        $this->sequenceHandler2 = $this->prophesize(SequenceHandlerInterface::class);
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function getIteratorShouldThrowHandlerExceptionBecauseNoHandlerSupportsTheData(): void
    {
        $data = new stdClass();
        $this->sequenceHandler1->supports($data)->willReturn(false)->shouldBeCalledOnce();
        $this->sequenceHandler2->supports($data)->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(new HandlerException(
            'The `' . CompositeSequenceHandler::class . '` has no supported handler. Illegal invocation of method' .
            ' `getIterator`. You should invoke the `supports` method first!'
        ));

        $this->getCompositeSequenceHandler()->getIterator($data);
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function filterShouldThrowHandlerExceptionBecauseNoHandlerSupportsTheData(): void
    {
        $data = new stdClass();
        $this->sequenceHandler1->supports($data)->willReturn(false)->shouldBeCalledOnce();
        $this->sequenceHandler2->supports($data)->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(new HandlerException(
            'The `' . CompositeSequenceHandler::class . '` has no supported handler. Illegal invocation of method' .
            ' `filter`. You should invoke the `supports` method first!'
        ));

        $this->getCompositeSequenceHandler()->filter($data, static function () {
        });
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function flattenShouldThrowHandlerExceptionBecauseNoHandlerSupportsTheData(): void
    {
        $data = new stdClass();
        $this->sequenceHandler1->supports($data)->willReturn(false)->shouldBeCalledOnce();
        $this->sequenceHandler2->supports($data)->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(new HandlerException(
            'The `' . CompositeSequenceHandler::class . '` has no supported handler. Illegal invocation of method' .
            ' `flatten`. You should invoke the `supports` method first!'
        ));

        $this->getCompositeSequenceHandler()->flatten($data, static function () {
        });
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function countShouldThrowHandlerExceptionBecauseNoHandlerSupportsTheData(): void
    {
        $data = new stdClass();
        $this->sequenceHandler1->supports($data)->willReturn(false)->shouldBeCalledOnce();
        $this->sequenceHandler2->supports($data)->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(new HandlerException(
            'The `' . CompositeSequenceHandler::class . '` has no supported handler. Illegal invocation of method' .
            ' `count`. You should invoke the `supports` method first!'
        ));

        $this->getCompositeSequenceHandler()->count($data);
    }

    /**
     * @test
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function supportShouldReturnFalseWhenThereIsNoHandlerWhichSupportsTheData(): void
    {
        $data = new stdClass();
        $this->sequenceHandler1->supports($data)->willReturn(false)->shouldBeCalledOnce();
        $this->sequenceHandler2->supports($data)->willReturn(false)->shouldBeCalledOnce();

        $this->assertFalse($this->getCompositeSequenceHandler()->supports($data));
    }

    /**
     * @test
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function supportShouldReturnTrueAsSoonAsAHandlerSupportsTheData(): void
    {
        $data = new stdClass();
        $this->sequenceHandler1->supports($data)->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler2->supports(Argument::any())->shouldNotBeCalled();

        $this->assertTrue($this->getCompositeSequenceHandler()->supports($data));
    }

    /**
     * @test
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function getIteratorShouldReturnTheIteratorGottenFromTheSupportedHandler(): void
    {
        $data = ['key' => 'value'];
        $iterator = new ArrayIterator($data);
        $this->sequenceHandler1->supports($data)->willReturn(false)->shouldBeCalledOnce();
        $this->sequenceHandler1->getIterator(Argument::any())->shouldNotBeCalled();
        $this->sequenceHandler2->supports($data)->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler2->getIterator($data)->willReturn($iterator)->shouldBeCalledOnce();

        $this->assertEquals($iterator, $this->getCompositeSequenceHandler()->getIterator($data));
    }

    /**
     * @test
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function filterShouldReturnTheFilteredResultFromTheSupportedHandler(): void
    {
        $data = ['key' => 'value'];
        $callback = static function () {
        };
        $filteredResult = ['filtered-result'];
        $this->sequenceHandler1->supports($data)->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler1->filter($data, $callback)->willReturn($filteredResult)->shouldBeCalledOnce();
        $this->sequenceHandler2->supports(Argument::any())->shouldNotBeCalled();
        $this->sequenceHandler2->getIterator(Argument::any())->shouldNotBeCalled();

        $this->assertEquals($filteredResult, $this->getCompositeSequenceHandler()->filter($data, $callback));
    }

    /**
     * @test
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function flattenShouldReturnTheFlattenResultFromTheSupportedHandler(): void
    {
        $data = ['key' => 'value'];
        $callback = static function () {
        };
        $filteredResult = ['filtered-result'];
        $this->sequenceHandler1->supports($data)->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler1->flatten($data, $callback)->willReturn($filteredResult)->shouldBeCalledOnce();
        $this->sequenceHandler2->supports(Argument::any())->shouldNotBeCalled();
        $this->sequenceHandler2->getIterator(Argument::any())->shouldNotBeCalled();

        $this->assertEquals($filteredResult, $this->getCompositeSequenceHandler()->flatten($data, $callback));
    }

    /**
     * @test
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function countShouldReturnTheFlattenResultFromTheSupportedHandler(): void
    {
        $data = ['key' => 'value'];
        $countResult = 3;
        $this->sequenceHandler1->supports($data)->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler1->count($data)->willReturn($countResult)->shouldBeCalledOnce();
        $this->sequenceHandler2->supports(Argument::any())->shouldNotBeCalled();
        $this->sequenceHandler2->getIterator(Argument::any())->shouldNotBeCalled();

        $this->assertEquals($countResult, $this->getCompositeSequenceHandler()->count($data));
    }

    /**
     * @return CompositeSequenceHandler
     * @throws ObjectProphecyException
     */
    private function getCompositeSequenceHandler(): CompositeSequenceHandler
    {
        return new CompositeSequenceHandler([$this->sequenceHandler1->reveal(), $this->sequenceHandler2->reveal()]);
    }
}
