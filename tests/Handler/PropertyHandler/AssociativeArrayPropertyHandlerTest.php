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
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Handler\PropertyHandler
 */
class AssociativeArrayPropertyHandlerTest extends TestCase
{
    /**
     * @test
     *
     * @throws HandlerException
     * @return void
     */
    public function getValueForPropertyNameShouldThrowHandlerExceptionBecauseCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . AssociativeArrayPropertyHandler::class . '` can only handle associative arrays. Illegal invocation of method ' .
            '`getValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getAssociativeArrayPropertyHandler()->getValueForPropertyName('my-prop', null);
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @return void
     */
    public function hasValueForPropertyNameShouldThrowHandlerExceptionBecauseCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . AssociativeArrayPropertyHandler::class . '` can only handle associative arrays. Illegal ' .
            'invocation of method `hasValueForPropertyName`. You should invoke the `supports` method first!'
        ));

        $this->getAssociativeArrayPropertyHandler()->hasValueForPropertyName('my-prop', null);
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @return void
     */
    public function getValueForPropertyNameShouldThrowHandlerExceptionWhenItSupportsButPropertyNameNotFound(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . AssociativeArrayPropertyHandler::class . '` can not find a value for property name `my-prop`.' .
            ' Illegal invocation of method `getValueForPropertyName`. You should invoke the `hasValueForPropertyName`' .
            ' method first!'
        ));

        $this->getAssociativeArrayPropertyHandler()->getValueForPropertyName('my-prop', ['key' => 'value']);
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
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
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
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
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function getValueForPropertyNameShouldReturnTheFoundValue(): void
    {
        $this->assertEquals(
            'value2',
            $this->getAssociativeArrayPropertyHandler()->getValueForPropertyName(
                'my-prop',
                ['key' => 'value', 'my-prop' => 'value2']
            )
        );
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function hasValueForPropertyNameShouldReturnFalseWhenNotFoundValue(): void
    {
        $this->assertFalse(
            $this->getAssociativeArrayPropertyHandler()->hasValueForPropertyName('my-prop', ['key' => 'value'])
        );
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function hasValueForPropertyNameShouldReturnTrueWhenFoundValue(): void
    {
        $this->assertTrue(
            $this->getAssociativeArrayPropertyHandler()->hasValueForPropertyName('key', ['key' => 'value'])
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