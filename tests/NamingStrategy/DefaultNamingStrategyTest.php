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

namespace tests\Jojo1981\DataResolver\NamingStrategy;

use Jojo1981\DataResolver\NamingStrategy\DefaultNamingStrategy;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\NamingStrategy
 */
final class DefaultNamingStrategyTest extends TestCase
{
    /** @var DefaultNamingStrategy */
    private DefaultNamingStrategy $namingStrategy;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->namingStrategy = new DefaultNamingStrategy();
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testGetPropertyNamesShouldReturnSnakeCaseAndCamelCasePropertyNames(): void
    {
        self::assertEquals(['name'], $this->namingStrategy->getPropertyNames('name'));
        self::assertEquals(['my_name', 'myName'], $this->namingStrategy->getPropertyNames('my_name'));
        self::assertEquals(['my_name', 'myName'], $this->namingStrategy->getPropertyNames('myName'));
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testGetMethodNamesShouldReturnOneCamelCaseGetterMethodNameInAnArray(): void
    {
        self::assertEquals(['getName'], $this->namingStrategy->getMethodNames('name'));
        self::assertEquals(['getMyName'], $this->namingStrategy->getMethodNames('my_name'));
        self::assertEquals(['getMyName'], $this->namingStrategy->getMethodNames('myName'));
    }
}
