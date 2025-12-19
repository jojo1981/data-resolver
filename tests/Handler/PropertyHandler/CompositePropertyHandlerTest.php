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

namespace tests\Jojo1981\DataResolver\Handler\PropertyHandler;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\PropertyHandler\CompositePropertyHandler;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @package tests\Jojo1981\DataResolver\Handler\PropertyHandler
 */
final class CompositePropertyHandlerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<NamingStrategyInterface> */
    private ObjectProphecy $namingStrategy;

    /** @var ObjectProphecy<PropertyHandlerInterface> */
    private ObjectProphecy $propertyHandler1;

    /** @var ObjectProphecy<PropertyHandlerInterface> */
    private ObjectProphecy $propertyHandler2;

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
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function testGetValueForPropertyNameShouldThrowHandlerExceptionBecauseCalledWithUnsupportedData(): void
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
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function testHasValueForPropertyNameShouldThrowHandlerExceptionBecauseCalledWithUnsupportedData(): void
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
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function testSupportShouldReturnFalseWhenThereIsNoHandlerWhichSupportsThePropertyName(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();
        $this->propertyHandler2->supports($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();

        self::assertFalse($this->getCompositePropertyHandler()->supports($propertyName, $data));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     */
    public function testSupportShouldReturnTrueAsSoonAsAHandlerSupportsTheData(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(true)->shouldBeCalledOnce();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->propertyHandler2->supports(Argument::any(), Argument::any())->shouldNotBeCalled();

        self::assertTrue($this->getCompositePropertyHandler()->supports($propertyName, $data));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     */
    public function testGetValueForPropertyNameShouldReturnTheValueFromTheSupportedHandler(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler1->getValueForPropertyName(
            $this->namingStrategy,
            $propertyName,
            $data
        )->willReturn('FoundData')->shouldBeCalledOnce();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->propertyHandler2->supports(Argument::any(), Argument::any())->shouldNotBeCalled();


        self::assertEquals(
            'FoundData',
            $this->getCompositePropertyHandler()->getValueForPropertyName(
                $this->namingStrategy->reveal(),
                $propertyName,
                $data
            )
        );
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     */
    public function testHasValueForPropertyNameShouldReturnFalseWhenTheSupportedHandlerReturnFalse(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(true)->shouldBeCalledOnce();
        $this->propertyHandler1->hasValueForPropertyName(
            $this->namingStrategy,
            $propertyName,
            $data
        )->willReturn(false)->shouldBeCalledOnce();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->propertyHandler2->supports(Argument::any(), Argument::any())->shouldNotBeCalled();
        /** @noinspection PhpStrictTypeCheckingInspection */
        /** @noinspection PhpParamsInspection */
        $this->propertyHandler2->hasValueForPropertyName(
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldNotBeCalled();

        self::assertFalse($this->getCompositePropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            $propertyName,
            $data
        ));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @throws ExpectationFailedException
     */
    public function testHasValueForPropertyNameShouldReturnTrueWhenTheSupportedHandlerReturnTrue(): void
    {
        $propertyName = 'my-prop';
        $data = [];
        $this->propertyHandler1->supports($propertyName, $data)->willReturn(false)->shouldBeCalledOnce();
        /** @noinspection PhpStrictTypeCheckingInspection */
        /** @noinspection PhpParamsInspection */
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

        self::assertTrue($this->getCompositePropertyHandler()->hasValueForPropertyName(
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
