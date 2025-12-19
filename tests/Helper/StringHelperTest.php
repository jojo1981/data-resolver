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

namespace tests\Jojo1981\DataResolver\Helper;

use Jojo1981\DataResolver\Helper\StringHelper;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Helper
 */
final class StringHelperTest extends TestCase
{
    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testToCamelCaseShouldReturnTheStringPassedConvertedToCamelCase(): void
    {
        self::assertEquals('test', StringHelper::toCamelCase('test'));
        self::assertEquals('myTest', StringHelper::toCamelCase('myTest'));
        self::assertEquals('MyTest', StringHelper::toCamelCase('myTest', true));
        self::assertEquals('mySnakeCaseTest', StringHelper::toCamelCase('my_snake_case_test'));
        self::assertEquals('mySnakeCaseTest', StringHelper::toCamelCase('my-snake-case-test'));
        self::assertEquals('MySnakeCaseTest', StringHelper::toCamelCase('my snake case test', true));
        self::assertEquals('mySnakeCaseTest', StringHelper::toCamelCase('My-Snake-Case-Test'));
        self::assertEquals('mySnakeCaseTest', StringHelper::toCamelCase('My Snake-case Test'));
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testCamelToSnakeCaseShouldReturnTheStringPassedConvertedToSnakeCase(): void
    {
        self::assertEquals('test', StringHelper::toSnakeCase('test'));
        self::assertEquals('my_test', StringHelper::toSnakeCase('my_test'));
        self::assertEquals('my_test', StringHelper::toSnakeCase('myTest'));
        self::assertEquals('my_snake_case_test', StringHelper::toSnakeCase('mySnakeCaseTest'));
        self::assertEquals('my_snake_case_test', StringHelper::toSnakeCase('my Snake Case Test'));
        self::assertEquals('my_snake_case_test', StringHelper::toSnakeCase('my-Snake-Case Test'));
        self::assertEquals('my_snake_case_test', StringHelper::toSnakeCase('MY-SNAKE-CASE-TEST'));
    }
}
