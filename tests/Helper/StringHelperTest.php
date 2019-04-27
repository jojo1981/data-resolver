<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Helper;

use Jojo1981\DataResolver\Helper\StringHelper;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Helper
 */
class StringHelperTest extends TestCase
{
    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function toCamelCaseShouldReturnTheStringPassedConvertedToCamelCase(): void
    {
        $this->assertEquals('test', StringHelper::toCamelCase('test'));
        $this->assertEquals('myTest', StringHelper::toCamelCase('myTest'));
        $this->assertEquals('MyTest', StringHelper::toCamelCase('myTest', true));
        $this->assertEquals('mySnakeCaseTest', StringHelper::toCamelCase('my_snake_case_test'));
        $this->assertEquals('mySnakeCaseTest', StringHelper::toCamelCase('my-snake-case-test'));
        $this->assertEquals('MySnakeCaseTest', StringHelper::toCamelCase('my snake case test', true));
        $this->assertEquals('mySnakeCaseTest', StringHelper::toCamelCase('My-Snake-Case-Test'));
        $this->assertEquals('mySnakeCaseTest', StringHelper::toCamelCase('My Snake-case Test'));
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function camelToSnakeCaseShouldReturnTheStringPassedConvertedToSnakeCase(): void
    {
        $this->assertEquals('test', StringHelper::toSnakeCase('test'));
        $this->assertEquals('my_test', StringHelper::toSnakeCase('my_test'));
        $this->assertEquals('my_test', StringHelper::toSnakeCase('myTest'));
        $this->assertEquals('my_snake_case_test', StringHelper::toSnakeCase('mySnakeCaseTest'));
        $this->assertEquals('my_snake_case_test', StringHelper::toSnakeCase('my Snake Case Test'));
        $this->assertEquals('my_snake_case_test', StringHelper::toSnakeCase('my-Snake-Case Test'));
        $this->assertEquals('my_snake_case_test', StringHelper::toSnakeCase('MY-SNAKE-CASE-TEST'));
    }
}