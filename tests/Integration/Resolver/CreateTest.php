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

namespace tests\Jojo1981\DataResolver\Integration\Resolver;

use Jojo1981\DataResolver\Builder\ResolverBuilder;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use stdClass;
use tests\Jojo1981\DataResolver\Integration\AbstractIntegrationTestCase;

/**
 * @package tests\Jojo1981\DataResolver\Integration\Resolver
 */
final class CreateTest extends AbstractIntegrationTestCase
{
    /**
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     */
    #[CoversNothing]
    public function testCheckCreate(): void
    {
        $resolverBuilder = $this->getResolverBuilderFactory()->create();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(ResolverBuilder::class, $resolverBuilder);
        $resolver = $resolverBuilder->build();

        self::assertTrue($resolver->resolve(true));
        self::assertFalse($resolver->resolve(false));
        self::assertEquals(-1, $resolver->resolve(-1));
        self::assertEquals(1, $resolver->resolve(1));
        self::assertEquals(-1.2, $resolver->resolve(-1.2));
        self::assertEquals(1.2, $resolver->resolve(1.2));
        self::assertEquals(10, $resolver->resolve(10));
        self::assertEquals(new stdClass(), $resolver->resolve(new stdClass()));
        self::assertEquals('text', $resolver->resolve('text'));
        self::assertEquals('true', $resolver->resolve('true'));
        self::assertEquals('false', $resolver->resolve('false'));
        self::assertEquals('1', $resolver->resolve('1'));
        self::assertEquals([1, 2, 3], $resolver->resolve([1, 2, 3]));
        self::assertEquals(['zero', 'one', 'two'], $resolver->resolve(['zero', 'one', 'two']));
        self::assertEquals([1 => 'one', 2 => 'two'], $resolver->resolve([1 => 'one', 2 => 'two']));
        self::assertEquals(
            ['one' => 1, 'two' => 2, 'three' => 3],
            $resolver->resolve(['one' => 1, 'two' => 2, 'three' => 3])
        );
        self::assertEquals(0, $resolver->resolve(0));
        self::assertEquals('', $resolver->resolve(''));
        self::assertEquals('0', $resolver->resolve('0'));
        self::assertEquals(null, $resolver->resolve(null));
        self::assertEquals([], $resolver->resolve([]));
    }
}
