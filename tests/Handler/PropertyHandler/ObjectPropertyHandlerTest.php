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
use Jojo1981\DataResolver\Handler\PropertyHandler\ObjectPropertyHandler;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionException;
use stdClass;
use function define;

/**
 * @package tests\Jojo1981\DataResolver\Handler\PropertyHandler
 */
final class TestEntity
{
    /** @var string */
    public string $myProp;

    /** @var string */
    private string $myName;

    /**
     * @return string
     */
    public function getMyName(): string
    {
        return $this->myName;
    }

    /**
     * @param string $myName
     * @return void
     */
    public function setMyName(string $myName): void
    {
        $this->myName = $myName;
    }
}

/**
 * @package tests\Jojo1981\DataResolver\Handler\PropertyHandler
 */
final class ObjectPropertyHandlerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<NamingStrategyInterface> */
    private ObjectProphecy $namingStrategy;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->namingStrategy = $this->prophesize(NamingStrategyInterface::class);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testSupportShouldReturnFalseForDataWhichIsNotAnObject(): void
    {
        self::assertFalse($this->getObjectPropertyHandler()->supports('my-property', []));
        self::assertFalse($this->getObjectPropertyHandler()->supports('my-property', ['item1', 'item2', 'item2']));
        self::assertFalse($this->getObjectPropertyHandler()->supports('my-property', ['key1' => 'value1']));
        self::assertFalse($this->getObjectPropertyHandler()->supports('my-property', true));
        self::assertFalse($this->getObjectPropertyHandler()->supports('my-property', false));
        self::assertFalse($this->getObjectPropertyHandler()->supports('my-property', null));
        self::assertFalse($this->getObjectPropertyHandler()->supports('my-property', ''));
        self::assertFalse($this->getObjectPropertyHandler()->supports('my-property', 'text'));
        self::assertFalse($this->getObjectPropertyHandler()->supports('my-property', 25));
        self::assertFalse($this->getObjectPropertyHandler()->supports('my-property', 3.99));
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testSupportShouldReturnTrueForDataWhichIsAnObject(): void
    {
        self::assertTrue($this->getObjectPropertyHandler()->supports('my-property', new stdClass()));
        self::assertTrue($this->getObjectPropertyHandler()->supports('my-property', new TestEntity()));
    }

    /**
     * @return void
     * @throws ReflectionException
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    #[RunInSeparateProcess]
    public function testGetValueForPropertyNameShouldThrowHandlerExceptionBecauseReflectionExceptionOccurs(): void
    {
        $this->expectExceptionObject(new HandlerException('Can not get reflection'));

        define('FAKE_REFLECTION_EXCEPTION', true);
        $this->getObjectPropertyHandler()->getValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            new TestEntity()
        );
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    #[RunInSeparateProcess]
    public function testHasValueForPropertyNameShouldThrowHandlerExceptionBecauseReflectionExceptionOccurs(): void
    {
        $this->expectExceptionObject(new HandlerException('Can not get reflection'));

        define('FAKE_REFLECTION_EXCEPTION', true);
        $this->getObjectPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            new TestEntity()
        );
    }

    /**
     * @return void
     * @throws ReflectionException
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function testGetValueForPropertyNameShouldThrowHandlerExceptionBecauseCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . ObjectPropertyHandler::class . '` can only handle objects. Illegal invocation of method ' .
            '`getValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getObjectPropertyHandler()->getValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            []
        );
    }

    /**
     * @return void
     * @throws ReflectionException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testGetValueForPropertyNameShouldReturnNullWhenDataCanNotBeFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        self::assertNull($this->getObjectPropertyHandler()->getValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            new stdClass()
        ));
    }

    /**
     * @return void
     * @throws ReflectionException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testGetValueForPropertyNameShouldReturnFoundValueWhenDataCanBeFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        $data = new stdClass();
        $data->propertyName = 'MY-DATA';

        self::assertEquals(
            'MY-DATA',
            $this->getObjectPropertyHandler()->getValueForPropertyName(
                $this->namingStrategy->reveal(),
                'property-name',
                $data
            )
        );
    }

    /**
     * @return void
     * @throws ReflectionException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testGetValueForPropertyNameShouldReturnNullWhenDataCanNotBeFoundUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames('property-name')->willReturn(['getPropertyName'])->shouldBeCalledOnce();

        self::assertNull($this->getObjectPropertyHandler()->getValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            new TestEntity()
        ));
    }

    /**
     * @return void
     * @throws ReflectionException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testGetValueForPropertyNameShouldReturnFoundValueByPropertyNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('myProp')->willReturn(['getMyProp'])->shouldBeCalledOnce();
        $this->namingStrategy->getPropertyNames('myProp')->willReturn(['my_prop', 'myProp'])->shouldBeCalledOnce();

        $data = new TestEntity();
        $data->myProp = 'My-DaTa';

        self::assertEquals(
            'My-DaTa',
            $this->getObjectPropertyHandler()->getValueForPropertyName(
                $this->namingStrategy->reveal(),
                'myProp',
                $data
            )
        );
    }

    /**
     * @return void
     * @throws ReflectionException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testGetValueForPropertyNameShouldReturnFoundValueByMethodNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('my_name')->willReturn(['getMyName'])->shouldBeCalledOnce();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->namingStrategy->getPropertyNames(Argument::any())->shouldNotBeCalled();

        $data = new TestEntity();
        $data->setMyName('TheName');

        self::assertEquals(
            'TheName',
            $this->getObjectPropertyHandler()->getValueForPropertyName(
                $this->namingStrategy->reveal(),
                'my_name',
                $data
            )
        );
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function testHasValueForPropertyNameShouldThrowHandlerExceptionBecauseCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . ObjectPropertyHandler::class . '` can only handle objects. Illegal invocation of method ' .
            '`hasValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getObjectPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            []
        );
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testHasValueForPropertyNameShouldReturnFalseWhenDataCanNotBeFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        self::assertFalse($this->getObjectPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            new stdClass()
        ));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testHasValueForPropertyNameShouldReturnTrueWhenValueFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        $data = new stdClass();
        $data->propertyName = 'MY-DATA';

        self::assertTrue($this->getObjectPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            $data
        ));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testHasValueForPropertyNameShouldReturnFalseWhenDataCanNotBeFoundUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames('property-name')->willReturn(['getPropertyName'])->shouldBeCalledOnce();

        self::assertFalse($this->getObjectPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            new TestEntity()
        ));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testHasValueForPropertyNameShouldReturnTrueWhenFoundValueByPropertyNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('myProp')->willReturn(['getMyProp'])->shouldBeCalledOnce();
        $this->namingStrategy->getPropertyNames('myProp')->willReturn(['my_prop', 'myProp'])->shouldBeCalledOnce();

        $data = new TestEntity();
        $data->myProp = 'My-DaTa';

        self::assertTrue($this->getObjectPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'myProp',
            $data
        ));
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testHasValueForPropertyNameShouldReturnTrueWhenFoundValueByMethodNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('my_name')->willReturn(['getMyName'])->shouldBeCalledOnce();
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->namingStrategy->getPropertyNames(Argument::any())->shouldNotBeCalled();

        $data = new TestEntity();
        $data->setMyName('TheName');

        self::assertTrue($this->getObjectPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'my_name',
            $data
        ));
    }

    /**
     * @return ObjectPropertyHandler
     */
    private function getObjectPropertyHandler(): ObjectPropertyHandler
    {
        return new ObjectPropertyHandler();
    }
}
