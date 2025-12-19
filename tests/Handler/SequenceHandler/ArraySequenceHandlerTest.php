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

namespace tests\Jojo1981\DataResolver\Handler\SequenceHandler;

use ArrayIterator;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandler\ArraySequenceHandler;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @package tests\Jojo1981\DataResolver\Handler\SequenceHandler
 */
final class ArraySequenceHandlerTest extends TestCase
{
    /**
     * @return void
     * @throws HandlerException
     */
    public function testGetIteratorShouldThrowHandlerExceptionWhenCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . ArraySequenceHandler::class . '` can only handle indexed arrays. Illegal invocation ' .
            'of method `getIterator`. You should invoke the `supports` method first!'
        ));

        $this->getArraySequenceHandler()->getIterator(null);
    }

    /**
     * @return void
     * @throws HandlerException
     */
    public function testFilterShouldThrowHandlerExceptionWhenCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . ArraySequenceHandler::class . '` can only handle indexed arrays. Illegal invocation ' .
            'of method `filter`. You should invoke the `supports` method first!'
        ));

        $this->getArraySequenceHandler()->filter(null, static function () {
        });
    }

    /**
     * @return void
     * @throws HandlerException
     */
    public function testFlattenShouldThrowHandlerExceptionWhenCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . ArraySequenceHandler::class . '` can only handle indexed arrays. Illegal invocation ' .
            'of method `flatten`. You should invoke the `supports` method first!'
        ));

        $this->getArraySequenceHandler()->flatten(null, static function () {
        });
    }

    /**
     * @return void
     * @throws HandlerException
     */
    public function testCountShouldThrowHandlerExceptionWhenCalledWithUnsupportedData(): void
    {
        $this->expectExceptionObject(new HandlerException(
            'The `' . ArraySequenceHandler::class . '` can only handle indexed arrays. Illegal invocation ' .
            'of method `count`. You should invoke the `supports` method first!'
        ));

        $this->getArraySequenceHandler()->count(null);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testSupportsShouldReturnFalseWhenDataIsNotAnIndexedArray(): void
    {
        self::assertFalse($this->getArraySequenceHandler()->supports(['key' => 'value']));
        self::assertFalse($this->getArraySequenceHandler()->supports(null));
        self::assertFalse($this->getArraySequenceHandler()->supports(new stdClass()));
        self::assertFalse($this->getArraySequenceHandler()->supports(''));
        self::assertFalse($this->getArraySequenceHandler()->supports('text'));
        self::assertFalse($this->getArraySequenceHandler()->supports(10));
        self::assertFalse($this->getArraySequenceHandler()->supports(3.25));
        self::assertFalse($this->getArraySequenceHandler()->supports(true));
        self::assertFalse($this->getArraySequenceHandler()->supports(false));
        self::assertFalse($this->getArraySequenceHandler()->supports(new ArrayIterator()));
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testSupportsShouldReturnTrueWhenDataIsAnIndexedArray(): void
    {
        self::assertTrue($this->getArraySequenceHandler()->supports([]));
        self::assertTrue($this->getArraySequenceHandler()->supports([[]]));
        self::assertTrue($this->getArraySequenceHandler()->supports([['key' => 'value']]));
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     */
    public function testGetIteratorShouldReturnAnArrayIteratorWhenDataIsSupported(): void
    {
        $data = [['name' => 'item1'], ['name' => 'item2']];
        /** @var ArrayIterator $iterator */
        $iterator = $this->getArraySequenceHandler()->getIterator($data);
        self::assertInstanceOf(ArrayIterator::class, $iterator);
        self::assertEquals($data, $iterator->getArrayCopy());
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testFilterShouldReturnTheFilteredResultWhenDataIsSupported(): void
    {
        $data = [['name' => 'item1'], ['name' => 'item2'], ['name' => 'item3']];

        $calledTimes = 0;
        $callback = function ($value, $key) use (&$calledTimes): bool {
            $expectedCallArguments = [
                [['name' => 'item1'], 0],
                [['name' => 'item2'], 1],
                [['name' => 'item3'], 2]
            ];
            self::assertEquals($value, $expectedCallArguments[$calledTimes][0]);
            self::assertEquals($key, $expectedCallArguments[$calledTimes][1]);
            $calledTimes++;

            return 'item2' !== $value['name'];
        };

        $expected = [0 => ['name' => 'item1'], 2 => ['name' => 'item3']];
        self::assertEquals($expected, $this->getArraySequenceHandler()->filter($data, $callback));
        self::assertEquals(3, $calledTimes);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testFlattenShouldReturnTheFlattenResultWhenDataIsSupported(): void
    {
        $data = [['name' => ['item1']], ['name' => ['item2.1', 'item2.2']], ['name' => ['item3']]];
        $flattenData = ['item1', 'item2.1', 'item2.2', 'item3'];

        $calledTimes = 0;
        $callback = function ($value, $key) use (&$calledTimes) {
            $expectedCallArguments = [
                [['name' => ['item1']], 0],
                [['name' => ['item2.1', 'item2.2']], 1],
                [['name' => ['item3']], 2]
            ];
            self::assertEquals($value, $expectedCallArguments[$calledTimes][0]);
            self::assertEquals($key, $expectedCallArguments[$calledTimes][1]);
            $calledTimes++;

            return $value['name'];
        };

        self::assertEquals($flattenData, $this->getArraySequenceHandler()->flatten($data, $callback));
        self::assertEquals(3, $calledTimes);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testFlattenShouldIgnoreNullAnEmptyArrayValueAndHandleAssociativeArrayValueFromCallback(): void
    {
        $data = ['item1', 'item2', 'item3', 'item4', 'item5', 'item6', 'item7', 'item8'];
        $flattenData = ['item2', 'item3.1', 'item3.2', 'item5', 'item6.1', 'item6.2', false, true];

        $calledTimes = 0;
        $callback = function ($value, $key) use (&$calledTimes) {
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
            self::assertEquals($value, $expectedCallArguments[$calledTimes][0]);
            self::assertEquals($key, $expectedCallArguments[$calledTimes][1]);
            $result = $expectedCallArguments[$calledTimes][2];
            $calledTimes++;

            return $result;
        };

        self::assertEquals($flattenData, $this->getArraySequenceHandler()->flatten($data, $callback));
        self::assertEquals(8, $calledTimes);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     * @throws HandlerException
     */
    public function testCountShouldReturnTheCountResultWhenDataIsSupported(): void
    {
        $data = [['name' => ['item1']], ['name' => ['item2.1', 'item2.2']], ['name' => ['item3']]];
        self::assertEquals(0, $this->getArraySequenceHandler()->count([]));
        self::assertEquals(3, $this->getArraySequenceHandler()->count($data));
    }

    /**
     * @return ArraySequenceHandler
     */
    private function getArraySequenceHandler(): ArraySequenceHandler
    {
        return new ArraySequenceHandler();
    }
}
