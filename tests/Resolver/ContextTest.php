<?php declare(strict_types=1);
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
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Resolver
 */
final class ContextTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function constructWithoutPathShouldBeInitializedWithAnEmptyStringPath(): void
    {
        $context = new Context('my-name');
        $this->assertEquals('', $context->getPath());
    }

    /**
     * @test
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
