<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Handler\SequenceHandler;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandler\ArraySequenceHandler;
use PHPUnit\Framework\ExpectationFailedException;
use tests\Jojo1981\DataResolver\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Handler\SequenceHandler
 */
class ArraySequenceHandlerTest extends TestCase
{
    /**
     * @test
     *
     * @throws HandlerException
     * @return void
     */
    public function getIteratorShouldThrowHandlerExceptionWhenCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . ArraySequenceHandler::class . '` can only handle indexed arrays. Illegal invocation ' .
            'of method `getIterator`. You should invoke the `supports` method first!'
        ));

        $this->getArraySequenceHandler()->getIterator(null);
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @return void
     */
    public function filterShouldThrowHandlerExceptionWhenCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . ArraySequenceHandler::class . '` can only handle indexed arrays. Illegal invocation ' .
            'of method `filter`. You should invoke the `supports` method first!'
        ));

        $this->getArraySequenceHandler()->filter(null, static function () {});
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @return void
     */
    public function flattenShouldThrowHandlerExceptionWhenCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . ArraySequenceHandler::class . '` can only handle indexed arrays. Illegal invocation ' .
            'of method `flatten`. You should invoke the `supports` method first!'
        ));

        $this->getArraySequenceHandler()->flatten(null, static function () {});
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @return void
     */
    public function countShouldThrowHandlerExceptionWhenCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . ArraySequenceHandler::class . '` can only handle indexed arrays. Illegal invocation ' .
            'of method `count`. You should invoke the `supports` method first!'
        ));

        $this->getArraySequenceHandler()->count(null);
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function supportsShouldReturnFalseWhenDataIsNotAnIndexedArray(): void
    {
        $this->assertFalse($this->getArraySequenceHandler()->supports(['key' => 'value']));
        $this->assertFalse($this->getArraySequenceHandler()->supports(null));
        $this->assertFalse($this->getArraySequenceHandler()->supports(new \stdClass()));
        $this->assertFalse($this->getArraySequenceHandler()->supports(''));
        $this->assertFalse($this->getArraySequenceHandler()->supports('text'));
        $this->assertFalse($this->getArraySequenceHandler()->supports(10));
        $this->assertFalse($this->getArraySequenceHandler()->supports(3.25));
        $this->assertFalse($this->getArraySequenceHandler()->supports(true));
        $this->assertFalse($this->getArraySequenceHandler()->supports(false));
        $this->assertFalse($this->getArraySequenceHandler()->supports(new \ArrayIterator()));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function supportsShouldReturnTrueWhenDataIsAnIndexedArray(): void
    {
        $this->assertTrue($this->getArraySequenceHandler()->supports([]));
        $this->assertTrue($this->getArraySequenceHandler()->supports([[]]));
        $this->assertTrue($this->getArraySequenceHandler()->supports([['key' => 'value']]));
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function getIteratorShouldReturnAnArrayIteratorWhenDataIsSupported(): void
    {
        $data = [['name' => 'item1'], ['name' => 'item2']];
        /** @var \ArrayIterator $iterator */
        $iterator = $this->getArraySequenceHandler()->getIterator($data);
        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
        $this->assertEquals($data, $iterator->getArrayCopy());
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function filterShouldReturnTheFilteredResultWhenDataIsSupported(): void
    {
        $data = [['name' => 'item1'], ['name' => 'item2'], ['name' => 'item3']];

        $calledTimes = 0;
        $expectedCallArguments = [
            [['name' => 'item1'], 0],
            [['name' => 'item2'], 1],
            [['name' => 'item3'], 2]
        ];
        $callback = function ($value, $key) use (&$calledTimes, $expectedCallArguments): bool {
            $this->assertEquals($value, $expectedCallArguments[$calledTimes][0]);
            $this->assertEquals($key, $expectedCallArguments[$calledTimes][1]);
            $calledTimes++;

            return 'item2' !== $value['name'];
        };

        $expected = [0 => ['name' => 'item1'], 2 => ['name' => 'item3']];
        $this->assertEquals($expected, $this->getArraySequenceHandler()->filter($data, $callback));
        $this->assertEquals(3, $calledTimes);
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function flattenShouldReturnTheFlattenResultWhenDataIsSupported(): void
    {
        $data = [['name' => ['item1']], ['name' => ['item2.1', 'item2.2']], ['name' => ['item3']]];
        $flattenData = ['item1', 'item2.1', 'item2.2', 'item3'];

        $calledTimes = 0;
        $expectedCallArguments = [
            [['name' => ['item1']], 0],
            [['name' => ['item2.1', 'item2.2']], 1],
            [['name' => ['item3']], 2]
        ];
        $callback = function ($value, $key) use (&$calledTimes, $expectedCallArguments) {
            $this->assertEquals($value, $expectedCallArguments[$calledTimes][0]);
            $this->assertEquals($key, $expectedCallArguments[$calledTimes][1]);
            $calledTimes++;

            return $value['name'];
        };

        $this->assertEquals($flattenData, $this->getArraySequenceHandler()->flatten($data, $callback));
        $this->assertEquals(3, $calledTimes);
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function flattenShouldIgnoreNullAnEmptyArrayValueAndHandleAssociativeArrayValueFromCallback(): void
    {
        $data = ['item1', 'item2', 'item3', 'item4', 'item5', 'item6', 'item7', 'item8'];
        $flattenData = ['item2', 'item3.1', 'item3.2', 'item5', 'item6.1', 'item6.2', false, true];

        $calledTimes = 0;
        $expectedCallArguments = [
            ['item1', 0, null],
            ['item2', 1, 'item2'],
            ['item3', 2, []],
            ['item4', 3, ['item3.1', 'item3.2']],
            ['item5', 4, 'item5'],
            ['item6', 5, ['key1' => 'item6.1', 'key2' => 'item6.2']],
            ['item7', 6, false],
            ['item8', 7, true]
        ];
        $callback = function ($value, $key) use (&$calledTimes, $expectedCallArguments) {
            $this->assertEquals($value, $expectedCallArguments[$calledTimes][0]);
            $this->assertEquals($key, $expectedCallArguments[$calledTimes][1]);
            $result = $expectedCallArguments[$calledTimes][2];
            $calledTimes++;

            return $result;
        };

        $this->assertEquals($flattenData, $this->getArraySequenceHandler()->flatten($data, $callback));
        $this->assertEquals(8, $calledTimes);
    }

    /**
     * @test
     *
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function countShouldReturnTheCountResultWhenDataIsSupported(): void
    {
        $data = [['name' => ['item1']], ['name' => ['item2.1', 'item2.2']], ['name' => ['item3']]];
        $this->assertEquals(0, $this->getArraySequenceHandler()->count([]));
        $this->assertEquals(3, $this->getArraySequenceHandler()->count($data));
    }

    /**
     * @return ArraySequenceHandler
     */
    private function getArraySequenceHandler(): ArraySequenceHandler
    {
        return new ArraySequenceHandler();
    }
}