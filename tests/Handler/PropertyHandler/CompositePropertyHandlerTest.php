<?php declare(strict_types=1);
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
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use PHPUnit\Framework\ExpectationFailedException;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use tests\Jojo1981\DataResolver\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Handler\PropertyHandler
 */
class CompositePropertyHandlerTest extends TestCase
{
    /** @var ObjectProphecy|NamingStrategyInterface */
    private $namingStrategy;

    /** @var ObjectProphecy|PropertyHandlerInterface */
    private $propertyHandler1;

    /** @var ObjectProphecy|PropertyHandlerInterface */
    private $propertyHandler2;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->namingStrategy = $this->prophesize(NamingStrategyInterface::class);
        $this->propertyHandler1 = $this->prophesize(PropertyHandlerInterface::class);
        $this->propertyHandler2 = $this->prophesize(PropertyHandlerInterface::class);
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
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

        $this->getCompositePropertyHandler()->getValueForPropertyName(
            $this->namingStrategy->reveal(),
            $propertyName,
            $data
        );
    }

    /**
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
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

        $this->getCompositePropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            $propertyName,
            $data
        );
    }

    /**
     * @test
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
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
     * @return void
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
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
     * @return void
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function getValueForPropertyNameShouldReturnTheValueFromTheSupportedHandler(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler1->getValueForPropertyName(
            $this->namingStrategy,
            $propertyName,
            $data
        )->willReturn('FoundData')->shouldBeCalledOnce();
        $this->propertyHandler2->supports(Argument::any(), Argument::any())->shouldNotBeCalled();


        $this->assertEquals(
            'FoundData',
            $this->getCompositePropertyHandler()->getValueForPropertyName(
                $this->namingStrategy->reveal(),
                $propertyName,
                $data
            )
        );
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function hasValueForPropertyNameShouldReturnFalseWhenTheSupportedHandlerReturnFalse(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler1->hasValueForPropertyName(
            $this->namingStrategy,
            $propertyName,
            $data
        )->willReturn(false)->shouldBeCalledOnce();
        $this->propertyHandler2->supports(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->propertyHandler2->hasValueForPropertyName(
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldNotBeCalled();

        $this->assertFalse($this->getCompositePropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            $propertyName,
            $data
        ));
    }

    /**
     * @test
     *
     * @return void
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function hasValueForPropertyNameShouldReturnTrueWhenTheSupportedHandlerReturnTrue(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();
        $this->propertyHandler1->hasValueForPropertyName(
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldNotBeCalled();
        $this->propertyHandler2->supports($propertyName, $data)->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler2->hasValueForPropertyName(
            $this->namingStrategy,
            $propertyName,
            $data
        )->willReturn(true)->shouldBeCalledOnce();

        $this->assertTrue($this->getCompositePropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            $propertyName,
            $data
        ));
    }

    /**
     * @return CompositePropertyHandler
     * @throws ObjectProphecyException
     */
    private function getCompositePropertyHandler(): CompositePropertyHandler
    {
        return new CompositePropertyHandler([$this->propertyHandler1->reveal(), $this->propertyHandler2->reveal()]);
    }
}
