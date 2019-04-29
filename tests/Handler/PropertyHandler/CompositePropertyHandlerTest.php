<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Handler\PropertyHandler;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\PropertyHandler\CompositePropertyHandler;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
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
 * @package tests\Jojo1981\DataResolver\Handler\PropertyHandler
 */
class CompositePropertyHandlerTest extends TestCase
{
    /** @var ObjectProphecy|PropertyHandlerInterface */
    private $propertyHandler1;

    /** @var ObjectProphecy|PropertyHandlerInterface */
    private $propertyHandler2;

    /**
     * @throws DoubleException
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @return void
     */
    protected function setUp(): void
    {
        $this->propertyHandler1 = $this->prophesize(PropertyHandlerInterface::class);
        $this->propertyHandler2 = $this->prophesize(PropertyHandlerInterface::class);
    }

    /**
     * @test
     *
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @return void
     */
    public function getValueForPropertyNameShouldThrowHandlerExceptionBecauseCalledWithUnsupportedData(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();
        $this->propertyHandler2->supports($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(new HandlerException(
            'The `' . CompositePropertyHandler::class . '` has no supported handler. Illegal invocation of method ' .
            '`getValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getCompositePropertyHandler()->getValueForPropertyName($propertyName, $data);
    }

    /**
     * @test
     *
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @return void
     */
    public function hasValueForPropertyNameShouldThrowHandlerExceptionBecauseCalledWithUnsupportedData(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();
        $this->propertyHandler2->supports($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();

        $this->expectExceptionObject(new HandlerException(
            'The `' . CompositePropertyHandler::class . '` has no supported handler. Illegal invocation of method ' .
            '`hasValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getCompositePropertyHandler()->hasValueForPropertyName($propertyName, $data);
    }

    /**
     * @test
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @return void
     */
    public function supportShouldReturnFalseWhenThereIsNoHandlerWhichSupportsThePropertyName(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();
        $this->propertyHandler2->supports($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();

        $this->assertFalse($this->getCompositePropertyHandler()->supports($propertyName, $data));
    }

    /**
     * @test
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @return void
     */
    public function supportShouldReturnTrueAsSoonAsAHandlerSupportsTheData(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler2->supports(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->assertTrue($this->getCompositePropertyHandler()->supports($propertyName, $data));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     * @return void
     */
    public function getValueForPropertyNameShouldReturnTheValueFromTheSupportedHandler(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler1->getValueForPropertyName($propertyName, $data)->willReturn('FoundData')->shouldBeCalledOnce();
        $this->propertyHandler2->supports(Argument::any(), Argument::any())->shouldNotBeCalled();


        $this->assertEquals('FoundData', $this->getCompositePropertyHandler()->getValueForPropertyName($propertyName, $data));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     * @return void
     */
    public function hasValueForPropertyNameShouldReturnFalseWhenTheSupportedHandlerReturnFalse(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler1->hasValueForPropertyName($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();
        $this->propertyHandler2->supports(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->propertyHandler2->hasValueForPropertyName(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->assertFalse($this->getCompositePropertyHandler()->hasValueForPropertyName($propertyName, $data));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     * @return void
     */
    public function hasValueForPropertyNameShouldReturnTrueWhenTheSupportedHandlerReturnTrue(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();
        $this->propertyHandler1->hasValueForPropertyName(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->propertyHandler2->supports($propertyName, $data)->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler2->hasValueForPropertyName($propertyName, $data)->willReturn(true)->shouldBeCalledOnce();

        $this->assertTrue($this->getCompositePropertyHandler()->hasValueForPropertyName($propertyName, $data));
    }

    /**
     * @throws ObjectProphecyException
     * @return CompositePropertyHandler
     */
    private function getCompositePropertyHandler(): CompositePropertyHandler
    {
        return new CompositePropertyHandler([$this->propertyHandler1->reveal(), $this->propertyHandler2->reveal()]);
    }
}