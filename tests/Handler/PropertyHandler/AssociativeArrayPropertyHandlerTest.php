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
use Jojo1981\DataResolver\Handler\PropertyHandler\AssociativeArrayPropertyHandler;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use PHPUnit\Framework\ExpectationFailedException;
use tests\Jojo1981\DataResolver\TestCase;
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
class AssociativeArrayPropertyHandlerTest extends TestCase
{
    /** @var ObjectProphecy|NamingStrategyInterface */
    private $namingStrategy;

    /**
     * @throws DoubleException
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @return void
     */
    protected function setUp(): void
    {
        $this->namingStrategy = $this->prophesize(NamingStrategyInterface::class);
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();
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
        $this->expectExceptionObject(new HandlerException(
            'The `' . AssociativeArrayPropertyHandler::class . '` can only handle associative arrays. Illegal invocation of method ' .
            '`getValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getAssociativeArrayPropertyHandler()->getValueForPropertyName($this->namingStrategy->reveal(), 'my-prop', null);
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
        $this->expectExceptionObject(new HandlerException(
            'The `' . AssociativeArrayPropertyHandler::class . '` can only handle associative arrays. Illegal ' .
            'invocation of method `hasValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getAssociativeArrayPropertyHandler()->hasValueForPropertyName($this->namingStrategy->reveal(), 'my-prop', null);
    }

    /**
     * @test
     *
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @return void
     */
    public function getValueForPropertyNameShouldThrowHandlerExceptionWhenItSupportsButPropertyNameNotFound(): void
    {
        $this->namingStrategy->getPropertyNames('my-prop')->willReturn(['my-prop', 'myProp'])->shouldBeCalledOnce();

        $this->expectExceptionObject(new HandlerException(
            'The `' . AssociativeArrayPropertyHandler::class . '` can not find a value for property name `my-prop`.' .
            ' Illegal invocation of method `getValueForPropertyName`. You should invoke the `hasValueForPropertyName`' .
            ' method first!'
        ));

        $this->getAssociativeArrayPropertyHandler()->getValueForPropertyName($this->namingStrategy->reveal(), 'my-prop', ['key' => 'value']);
    }

    /**
     * @test
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @return void
     */
    public function supportShouldReturnFalseWhenDataIsNotAnAssociativeArray():void
    {
        $this->assertFalse($this->getAssociativeArrayPropertyHandler()->supports('my-prop', null));
        $this->assertFalse($this->getAssociativeArrayPropertyHandler()->supports('my-prop', [['key' => 'value']]));
        $this->assertFalse($this->getAssociativeArrayPropertyHandler()->supports('my-prop', new \stdClass()));
        $this->assertFalse($this->getAssociativeArrayPropertyHandler()->supports('my-prop', ['key' => 'value', 'test']));
    }

    /**
     * @test
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @return void
     */
    public function supportShouldReturnTrueWhenDataIsAnAssociativeArray():void
    {
        $this->assertTrue($this->getAssociativeArrayPropertyHandler()->supports('my-prop', []));
        $this->assertTrue($this->getAssociativeArrayPropertyHandler()->supports('my-prop', ['key' => 'value']));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @return void
     */
    public function getValueForPropertyNameShouldReturnTheFoundValue(): void
    {
        $this->namingStrategy->getPropertyNames('my-prop')->willReturn(['my-prop', 'myProp'])->shouldBeCalledOnce();

        $this->assertEquals(
            'value2',
            $this->getAssociativeArrayPropertyHandler()->getValueForPropertyName(
                $this->namingStrategy->reveal(),
                'my-prop',
                ['key' => 'value', 'myProp' => 'value2']
            )
        );
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @return void
     */
    public function hasValueForPropertyNameShouldReturnFalseWhenNotFoundValue(): void
    {
        $this->namingStrategy->getPropertyNames('my-prop')->willReturn(['my-prop', 'myProp'])->shouldBeCalledOnce();

        $this->assertFalse(
            $this->getAssociativeArrayPropertyHandler()->hasValueForPropertyName(
                $this->namingStrategy->reveal(),
                'my-prop',
                ['key' => 'value']
            )
        );
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @return void
     */
    public function hasValueForPropertyNameShouldReturnTrueWhenFoundValue(): void
    {
        $this->namingStrategy->getPropertyNames('key')->willReturn(['key'])->shouldBeCalledOnce();

        $this->assertTrue(
            $this->getAssociativeArrayPropertyHandler()->hasValueForPropertyName(
                $this->namingStrategy->reveal(),
                'key',
                ['key' => 'value']
            )
        );
    }

    /**
     * @return AssociativeArrayPropertyHandler
     */
    private function getAssociativeArrayPropertyHandler(): AssociativeArrayPropertyHandler
    {
        return new AssociativeArrayPropertyHandler();
    }
}