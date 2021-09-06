<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\NamingStrategy;

use Jojo1981\DataResolver\NamingStrategy\DefaultNamingStrategy;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

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
     * @test
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function getPropertyNamesShouldReturnSnakeCaseAndCamelCasePropertyNames(): void
    {
        $this->assertEquals(['name'], $this->namingStrategy->getPropertyNames('name'));
        $this->assertEquals(['my_name', 'myName'], $this->namingStrategy->getPropertyNames('my_name'));
        $this->assertEquals(['my_name', 'myName'], $this->namingStrategy->getPropertyNames('myName'));
    }

    /**
     * @test
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function getMethodNamesShouldReturnOneCamelCaseGetterMethodNameInAnArray(): void
    {
        $this->assertEquals(['getName'], $this->namingStrategy->getMethodNames('name'));
        $this->assertEquals(['getMyName'], $this->namingStrategy->getMethodNames('my_name'));
        $this->assertEquals(['getMyName'], $this->namingStrategy->getMethodNames('myName'));
    }
}
