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
use Jojo1981\DataResolver\Handler\PropertyHandler\ObjectPropertyHandler;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
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
class TestEntity
{
    public $myProp;

    private $myName;

    public function getMyName()
    {
        return $this->myName;
    }

    public function setMyName($myName): void
    {
        $this->myName = $myName;
    }
}

/**
 * @package tests\Jojo1981\DataResolver\Handler\PropertyHandler
 */
class ObjectPropertyHandlerTest extends TestCase
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
    }

    /**
     * @test
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @return void
     */
    public function supportShouldReturnFalseForDataWhichIsNotAnObject(): void
    {
        $this->assertFalse($this->getObjectPropertyHandler()->supports('my-property', []));
        $this->assertFalse($this->getObjectPropertyHandler()->supports('my-property', ['item1', 'item2', 'item2']));
        $this->assertFalse($this->getObjectPropertyHandler()->supports('my-property', ['key1' => 'value1']));
        $this->assertFalse($this->getObjectPropertyHandler()->supports('my-property', true));
        $this->assertFalse($this->getObjectPropertyHandler()->supports('my-property', false));
        $this->assertFalse($this->getObjectPropertyHandler()->supports('my-property', null));
        $this->assertFalse($this->getObjectPropertyHandler()->supports('my-property', ''));
        $this->assertFalse($this->getObjectPropertyHandler()->supports('my-property', 'text'));
        $this->assertFalse($this->getObjectPropertyHandler()->supports('my-property', 25));
        $this->assertFalse($this->getObjectPropertyHandler()->supports('my-property', 3.99));
    }

    /**
     * @test
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @return void
     */
    public function supportShouldReturnTrueForDataWhichIsAnObject(): void
    {
        $this->assertTrue($this->getObjectPropertyHandler()->supports('my-property', new \stdClass()));
        $this->assertTrue($this->getObjectPropertyHandler()->supports('my-property', new TestEntity()));
    }

    /**
     * @test
     * @runInSeparateProcess
     *
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @return void
     */
    public function getValueForPropertyNameShouldThrowHandlerExceptionBecauseReflectionExceptionOccurs(): void
    {
        $this->expectExceptionObject(new HandlerException('Can not get reflection'));

        \define('FAKE_REFLECTION_EXCEPTION', true);
        $this->getObjectPropertyHandler()->getValueForPropertyName('property-name', new TestEntity());
    }

    /**
     * @test
     * @runInSeparateProcess
     *
     * @throws ObjectProphecyException
     * @throws HandlerException
     * @return void
     */
    public function hasValueForPropertyNameShouldThrowHandlerExceptionBecauseReflectionExceptionOccurs(): void
    {
        $this->expectExceptionObject(new HandlerException('Can not get reflection'));

        \define('FAKE_REFLECTION_EXCEPTION', true);
        $this->getObjectPropertyHandler()->hasValueForPropertyName('property-name', new TestEntity());
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
            'The `' . ObjectPropertyHandler::class . '` can only handle objects. Illegal invocation of method ' .
            '`getValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getObjectPropertyHandler()->getValueForPropertyName('property-name', []);
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
    public function getValueForPropertyNameShouldReturnNullWhenDataCanNotBeFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn(['property_name', 'propertyName'])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        $this->assertNull($this->getObjectPropertyHandler()->getValueForPropertyName('property-name', new \stdClass()));
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
    public function getValueForPropertyNameShouldReturnFoundValueWhenDataCanBeFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn(['property_name', 'propertyName'])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        $data = new \stdClass();
        $data->propertyName = 'MY-DATA';

        $this->assertEquals('MY-DATA', $this->getObjectPropertyHandler()->getValueForPropertyName('property-name', $data));
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
    public function getValueForPropertyNameShouldReturnNullWhenDataCanNotBeFoundUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn(['property_name', 'propertyName'])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames('property-name')->willReturn(['getPropertyName'])->shouldBeCalledOnce();

        $this->assertNull($this->getObjectPropertyHandler()->getValueForPropertyName('property-name', new TestEntity()));
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
    public function getValueForPropertyNameShouldReturnFoundValueByPropertyNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('myProp')->willReturn(['getMyProp'])->shouldBeCalledOnce();
        $this->namingStrategy->getPropertyNames('myProp')->willReturn(['my_prop', 'myProp'])->shouldBeCalledOnce();

        $data = new TestEntity();
        $data->myProp = 'My-DaTa';

        $this->assertEquals('My-DaTa', $this->getObjectPropertyHandler()->getValueForPropertyName('myProp', $data));
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
    public function getValueForPropertyNameShouldReturnFoundValueByMethodNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('my_name')->willReturn(['getMyName'])->shouldBeCalledOnce();
        $this->namingStrategy->getPropertyNames(Argument::any())->shouldNotBeCalled();

        $data = new TestEntity();
        $data->setMyName('TheName');

        $this->assertEquals('TheName', $this->getObjectPropertyHandler()->getValueForPropertyName('my_name', $data));
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
            'The `' . ObjectPropertyHandler::class . '` can only handle objects. Illegal invocation of method ' .
            '`hasValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getObjectPropertyHandler()->hasValueForPropertyName('property-name', []);
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
    public function hasValueForPropertyNameShouldReturnFalseWhenDataCanNotBeFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn(['property_name', 'propertyName'])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        $this->assertFalse($this->getObjectPropertyHandler()->hasValueForPropertyName('property-name', new \stdClass()));
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
    public function hasValueForPropertyNameShouldReturnTrueWhenValueFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn(['property_name', 'propertyName'])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        $data = new \stdClass();
        $data->propertyName = 'MY-DATA';

        $this->assertTrue($this->getObjectPropertyHandler()->hasValueForPropertyName('property-name', $data));
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
    public function hasValueForPropertyNameShouldReturnFalseWhenDataCanNotBeFoundUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn(['property_name', 'propertyName'])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames('property-name')->willReturn(['getPropertyName'])->shouldBeCalledOnce();

        $this->assertFalse($this->getObjectPropertyHandler()->hasValueForPropertyName('property-name', new TestEntity()));
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
    public function hasValueForPropertyNameShouldReturnTrueWhenFoundValueByPropertyNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('myProp')->willReturn(['getMyProp'])->shouldBeCalledOnce();
        $this->namingStrategy->getPropertyNames('myProp')->willReturn(['my_prop', 'myProp'])->shouldBeCalledOnce();

        $data = new TestEntity();
        $data->myProp = 'My-DaTa';

        $this->assertTrue($this->getObjectPropertyHandler()->hasValueForPropertyName('myProp', $data));
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
    public function hasValueForPropertyNameShouldReturnTrueWhenFoundValueByMethodNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('my_name')->willReturn(['getMyName'])->shouldBeCalledOnce();
        $this->namingStrategy->getPropertyNames(Argument::any())->shouldNotBeCalled();

        $data = new TestEntity();
        $data->setMyName('TheName');

        $this->assertTrue($this->getObjectPropertyHandler()->hasValueForPropertyName('my_name', $data));
    }

    /**
     * @throws ObjectProphecyException
     * @return ObjectPropertyHandler
     */
    private function getObjectPropertyHandler(): ObjectPropertyHandler
    {
        return new ObjectPropertyHandler($this->namingStrategy->reveal());
    }
}