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
use Jojo1981\DataResolver\Handler\PropertyHandler\AssociativeArrayPropertyHandler;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\InvalidArgumentException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

/**
 * @package tests\Jojo1981\DataResolver\Handler\PropertyHandler
 */
final class AssociativeArrayPropertyHandlerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<NamingStrategyInterface> */
    private ObjectProphecy $namingStrategy;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws InvalidArgumentException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->namingStrategy = $this->prophesize(NamingStrategyInterface::class);
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->namingStrategy->getMethodNames(Argument::any())->shouldNotBeCalled();
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function testGetValueForPropertyNameShouldThrowHandlerExceptionBecauseCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . AssociativeArrayPropertyHandler::class . '` can only handle associative arrays. Illegal invocation of method ' .
            '`getValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getAssociativeArrayPropertyHandler()->getValueForPropertyName(
            $this->namingStrategy->reveal(),
            'my-prop',
            null
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
            'The `' . AssociativeArrayPropertyHandler::class . '` can only handle associative arrays. Illegal ' .
            'invocation of method `hasValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getAssociativeArrayPropertyHandler()->hasValueForPropertyName(
            $this->namingStrategy->reveal(),
            'my-prop',
            null
        );
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     */
    public function testGetValueForPropertyNameShouldThrowHandlerExceptionWhenItSupportsButPropertyNameNotFound(): void
    {
        $this->namingStrategy->getPropertyNames('my-prop')->willReturn(['my-prop', 'myProp'])->shouldBeCalledOnce();

        $this->expectExceptionObject(new HandlerException(
            'The `' . AssociativeArrayPropertyHandler::class . '` can not find a value for property name `my-prop`.' .
            ' Illegal invocation of method `getValueForPropertyName`. You should invoke the `hasValueForPropertyName`' .
            ' method first!'
        ));

        $this->getAssociativeArrayPropertyHandler()->getValueForPropertyName(
            $this->namingStrategy->reveal(),
            'my-prop',
            ['key' => 'value']
        );
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testSupportShouldReturnFalseWhenDataIsNotAnAssociativeArray(): void
    {
        self::assertFalse($this->getAssociativeArrayPropertyHandler()->supports('my-prop', null));
        self::assertFalse($this->getAssociativeArrayPropertyHandler()->supports('my-prop', [['key' => 'value']]));
        self::assertFalse($this->getAssociativeArrayPropertyHandler()->supports('my-prop', new stdClass()));
        self::assertFalse($this->getAssociativeArrayPropertyHandler()->supports(
            'my-prop',
            ['key' => 'value', 'test']
        ));
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testSupportShouldReturnTrueWhenDataIsAnAssociativeArray(): void
    {
        self::assertTrue($this->getAssociativeArrayPropertyHandler()->supports('my-prop', []));
        self::assertTrue($this->getAssociativeArrayPropertyHandler()->supports('my-prop', ['key' => 'value']));
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     * @throws ObjectProphecyException
     * @throws HandlerException
     */
    public function testGetValueForPropertyNameShouldReturnTheFoundValue(): void
    {
        $this->namingStrategy->getPropertyNames('my-prop')->willReturn(['my-prop', 'myProp'])->shouldBeCalledOnce();

        self::assertEquals(
            'value2',
            $this->getAssociativeArrayPropertyHandler()->getValueForPropertyName(
                $this->namingStrategy->reveal(),
                'my-prop',
                ['key' => 'value', 'myProp' => 'value2']
            )
        );
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     * @throws ObjectProphecyException
     * @throws HandlerException
     */
    public function testHasValueForPropertyNameShouldReturnFalseWhenNotFoundValue(): void
    {
        $this->namingStrategy->getPropertyNames('my-prop')->willReturn(['my-prop', 'myProp'])->shouldBeCalledOnce();

        self::assertFalse(
            $this->getAssociativeArrayPropertyHandler()->hasValueForPropertyName(
                $this->namingStrategy->reveal(),
                'my-prop',
                ['key' => 'value']
            )
        );
    }

    /**
     * @return void
     * @throws ObjectProphecyException
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testHasValueForPropertyNameShouldReturnTrueWhenFoundValue(): void
    {
        $this->namingStrategy->getPropertyNames('key')->willReturn(['key'])->shouldBeCalledOnce();

        self::assertTrue(
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
