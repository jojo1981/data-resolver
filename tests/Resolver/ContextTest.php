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

namespace tests\Jojo1981\DataResolver\Resolver;

use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Resolver
 */
final class ContextTest extends TestCase
{
    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testCopyShouldReturnClonedInstance(): void
    {
        $originalContext = new Context('my-name', 'root.persons.0.name');
        $copiedContext = $originalContext->copy();

        self::assertEquals($copiedContext, $originalContext);
        self::assertNotSame($copiedContext, $originalContext);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testSetAndGetDataShouldWorkAndReturnSelf(): void
    {
        $context = new Context('my-name', 'root.persons.0.name');

        self::assertEquals('my-name', $context->getData());
        self::assertSame($context, $context->setData(null));
        self::assertEquals(null, $context->getData());
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testSetAndGetPathShouldWorkAndReturnSelf(): void
    {
        $context = new Context('my-name', 'root.persons.0.name');

        self::assertEquals('root.persons.0.name', $context->getPath());
        self::assertSame($context, $context->setPath('root.cars'));
        self::assertEquals('root.cars', $context->getPath());
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testConstructWithoutPathShouldBeInitializedWithAnEmptyStringPath(): void
    {
        $context = new Context('my-name');
        self::assertEquals('', $context->getPath());
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testPushAndPopPathPartShouldWorkAndReturnSelf(): void
    {
        $context = new Context('my-name', 'root.persons.0.name');

        self::assertEquals('root.persons.0.name', $context->getPath());
        self::assertSame($context, $context->popPathPart());
        self::assertEquals('root.persons.0', $context->getPath());
        self::assertSame($context, $context->pushPathPart('age'));
        self::assertEquals('root.persons.0.age', $context->getPath());
    }
}
