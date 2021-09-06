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
use Jojo1981\DataResolver\Handler\PropertyHandler\ObjectPropertyHandler;
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
use ReflectionException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
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

    /** @var ObjectProphecy|NamingStrategyInterface */
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
     * @test
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
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
     * @return void
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function supportShouldReturnTrueForDataWhichIsAnObject(): void
    {
        $this->assertTrue($this->getObjectPropertyHandler()->supports('my-property', new stdClass()));
        $this->assertTrue($this->getObjectPropertyHandler()->supports('my-property', new TestEntity()));
    }

    /**
     * @test
     * @runInSeparateProcess
     *
     * @return void
     * @throws ReflectionException
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function getValueForPropertyNameShouldThrowHandlerExceptionBecauseReflectionExceptionOccurs(): void
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
     * @test
     * @runInSeparateProcess
     *
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function hasValueForPropertyNameShouldThrowHandlerExceptionBecauseReflectionExceptionOccurs(): void
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
     * @test
     *
     * @return void
     * @throws ReflectionException
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function getValueForPropertyNameShouldThrowHandlerExceptionBecauseCalledWithUnsupportedData(): void
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
     * @test
     *
     * @return void
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function getValueForPropertyNameShouldReturnNullWhenDataCanNotBeFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        $this->assertNull($this->getObjectPropertyHandler()->getValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            new stdClass()
        ));
    }

    /**
     * @test
     *
     * @return void
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function getValueForPropertyNameShouldReturnFoundValueWhenDataCanBeFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        $data = new stdClass();
        $data->propertyName = 'MY-DATA';

        $this->assertEquals(
            'MY-DATA',
            $this->getObjectPropertyHandler()->getValueForPropertyName(
                $this->namingStrategy->reveal(),
                'property-name',
                $data
            )
        );
    }

    /**
     * @test
     *
     * @return void
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function getValueForPropertyNameShouldReturnNullWhenDataCanNotBeFoundUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames('property-name')->willReturn(['getPropertyName'])->shouldBeCalledOnce();

        $this->assertNull($this->getObjectPropertyHandler()->getValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            new TestEntity()
        ));
    }

    /**
     * @test
     *
     * @return void
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function getValueForPropertyNameShouldReturnFoundValueByPropertyNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('myProp')->willReturn(['getMyProp'])->shouldBeCalledOnce();
        $this->namingStrategy->getPropertyNames('myProp')->willReturn(['my_prop', 'myProp'])->shouldBeCalledOnce();

        $data = new TestEntity();
        $data->myProp = 'My-DaTa';

        $this->assertEquals(
            'My-DaTa',
            $this->getObjectPropertyHandler()->getValueForPropertyName(
                $this->namingStrategy->reveal(),
                'myProp',
                $data
            )
        );
    }

    /**
     * @test
     *
     * @return void
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function getValueForPropertyNameShouldReturnFoundValueByMethodNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('my_name')->willReturn(['getMyName'])->shouldBeCalledOnce();
        $this->namingStrategy->getPropertyNames(Argument::any())->shouldNotBeCalled();

        $data = new TestEntity();
        $data->setMyName('TheName');

        $this->assertEquals(
            'TheName',
            $this->getObjectPropertyHandler()->getValueForPropertyName(
                $this->namingStrategy->reveal(),
                'my_name',
                $data
            )
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
     * @test
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function hasValueForPropertyNameShouldReturnFalseWhenDataCanNotBeFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        $this->assertFalse($this->getObjectPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            new stdClass()
        ));
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
    public function hasValueForPropertyNameShouldReturnTrueWhenValueFoundUsingStdClassAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();

        $data = new stdClass();
        $data->propertyName = 'MY-DATA';

        $this->assertTrue($this->getObjectPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            $data
        ));
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
    public function hasValueForPropertyNameShouldReturnFalseWhenDataCanNotBeFoundUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getPropertyNames('property-name')->willReturn([
            'property_name',
            'propertyName'
        ])->shouldBeCalledOnce();
        $this->namingStrategy->getMethodNames('property-name')->willReturn(['getPropertyName'])->shouldBeCalledOnce();

        $this->assertFalse($this->getObjectPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'property-name',
            new TestEntity()
        ));
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
    public function hasValueForPropertyNameShouldReturnTrueWhenFoundValueByPropertyNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('myProp')->willReturn(['getMyProp'])->shouldBeCalledOnce();
        $this->namingStrategy->getPropertyNames('myProp')->willReturn(['my_prop', 'myProp'])->shouldBeCalledOnce();

        $data = new TestEntity();
        $data->myProp = 'My-DaTa';

        $this->assertTrue($this->getObjectPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'myProp',
            $data
        ));
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
    public function hasValueForPropertyNameShouldReturnTrueWhenFoundValueByMethodNameUsingTestEntityAsData(): void
    {
        $this->namingStrategy->getMethodNames('my_name')->willReturn(['getMyName'])->shouldBeCalledOnce();
        $this->namingStrategy->getPropertyNames(Argument::any())->shouldNotBeCalled();

        $data = new TestEntity();
        $data->setMyName('TheName');

        $this->assertTrue($this->getObjectPropertyHandler()->hasValueForPropertyName(
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
