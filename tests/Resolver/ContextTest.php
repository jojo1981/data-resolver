<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Resolver;

use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use tests\Jojo1981\DataResolver\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Resolver
 */
class ContextTest extends TestCase
{
    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function copyShouldReturnClonedInstance(): void
    {
        $originalContext = new Context('my-name', 'root.persons.0.name');
        $copiedContext = $originalContext->copy();

        $this->assertEquals($copiedContext, $originalContext);
        $this->assertNotSame($copiedContext, $originalContext);
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function setAndGetDataShouldWorkAndReturnSelf(): void
    {
        $context = new Context('my-name', 'root.persons.0.name');

        $this->assertEquals('my-name', $context->getData());
        $this->assertSame($context, $context->setData(null));
        $this->assertEquals(null, $context->getData());
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function setAndGetPathShouldWorkAndReturnSelf(): void
    {
        $context = new Context('my-name', 'root.persons.0.name');

        $this->assertEquals('root.persons.0.name', $context->getPath());
        $this->assertSame($context, $context->setPath('root.cars'));
        $this->assertEquals('root.cars', $context->getPath());
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function constructWithoutPathShouldBeInitializedWithAnEmptyStringPath(): void
    {
        $context = new Context('my-name');
        $this->assertEquals('', $context->getPath());
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function pushAndPopPathPartShouldWorkAndReturnSelf(): void
    {
        $context = new Context('my-name', 'root.persons.0.name');

        $this->assertEquals('root.persons.0.name', $context->getPath());
        $this->assertSame($context, $context->popPathPart());
        $this->assertEquals('root.persons.0', $context->getPath());
        $this->assertSame($context, $context->pushPathPart('age'));
        $this->assertEquals('root.persons.0.age', $context->getPath());
    }
}