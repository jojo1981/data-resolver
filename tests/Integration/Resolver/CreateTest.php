<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Integration\Resolver;

use Jojo1981\DataResolver\Builder\ResolverBuilder;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use stdClass;
use tests\Jojo1981\DataResolver\Integration\AbstractIntegrationTestCase;

/**
 * @package tests\Jojo1981\DataResolver\Integration\Resolver
 */
class CreateTest extends AbstractIntegrationTestCase
{
    /**
     * @test
     * @coversNothing
     *
     * @return void
     * @throws ExtractorException
     * @throws HandlerException
     * @throws InvalidArgumentException
     * @throws PredicateException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     */
    public function checkCreate(): void
    {
        $resolverBuilder = $this->getResolverBuilderFactory()->create();
        $this->assertInstanceOf(ResolverBuilder::class, $resolverBuilder);
        $resolver = $resolverBuilder->build();

        $this->assertEquals(true, $resolver->resolve(true));
        $this->assertEquals(false, $resolver->resolve(false));
        $this->assertEquals(-1, $resolver->resolve(-1));
        $this->assertEquals(1, $resolver->resolve(1));
        $this->assertEquals(-1.2, $resolver->resolve(-1.2));
        $this->assertEquals(1.2, $resolver->resolve(1.2));
        $this->assertEquals(10, $resolver->resolve(10));
        $this->assertEquals(new stdClass(), $resolver->resolve(new stdClass()));
        $this->assertEquals('text', $resolver->resolve('text'));
        $this->assertEquals('true', $resolver->resolve('true'));
        $this->assertEquals('false', $resolver->resolve('false'));
        $this->assertEquals('1', $resolver->resolve('1'));
        $this->assertEquals([1, 2, 3], $resolver->resolve([1, 2, 3]));
        $this->assertEquals(['zero', 'one', 'two'], $resolver->resolve(['zero', 'one', 'two']));
        $this->assertEquals([1 => 'one', 2 => 'two'], $resolver->resolve([1 => 'one', 2 => 'two']));
        $this->assertEquals(
            ['one' => 1, 'two' => 2, 'three' => 3],
            $resolver->resolve(['one' => 1, 'two' => 2, 'three' => 3])
        );
        $this->assertEquals(0, $resolver->resolve(0));
        $this->assertEquals('', $resolver->resolve(''));
        $this->assertEquals('0', $resolver->resolve('0'));
        $this->assertEquals(null, $resolver->resolve(null));
        $this->assertEquals([], $resolver->resolve([]));
    }
}
