<?php declare(strict_types=1);
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
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use tests\Jojo1981\DataResolver\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Helper
 */
class StringHelperTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
