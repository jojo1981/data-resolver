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
use Jojo1981\DataResolver\Exception\ResolverException;
use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use stdClass;
use tests\Jojo1981\DataResolver\Integration\AbstractIntegrationTestCase;
use function json_decode;

/**
 * @package tests\Jojo1981\DataResolver\Integration\Resolver
 */
final class ComposeTest extends AbstractIntegrationTestCase
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
    public function checkComposeWithSimpleResolver(): void
    {
        $resolverBuilder = $this->getResolverBuilderFactory()->compose($this->getResolverBuilderFactory()->create());
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

    /**
     * @return void
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExpectationFailedException
     * @throws ResolverException
     * @throws ExtractorException
     */
    #[CoversNothing]
    public function testCheckComposeWithComplexResolver(): void
    {
        $resolverBuilder1 = $this->getResolverBuilderFactory()->get('addresses');

        $resolverBuilder2 = $this->getResolverBuilderFactory()
            ->compose($resolverBuilder1)
            ->filter(
                $this->getResolverBuilderFactory()->where('city')->equals('New York')
            );

        $resolver1 = $resolverBuilder1->build();
        $resolver2 = $resolverBuilder2->build();

        $personObject = $this->getPersonAsObject();
        self::assertEquals($personObject->addresses, $resolver1->resolve($personObject));
        self::assertEquals([1 => $personObject->addresses[1]], $resolver2->resolve($personObject));

        $personArray = $this->getPersonAsAssociativeArray();
        self::assertEquals($personArray['addresses'], $resolver1->resolve($personArray));
        self::assertEquals([1 => $personArray['addresses'][1]], $resolver2->resolve($personArray));
    }

    /**
     * @return stdClass
     */
    private function getPersonAsObject(): stdClass
    {
        return json_decode($this->getPersonJsonString(), false);
    }

    /**
     * @return array[]
     */
    private function getPersonAsAssociativeArray(): array
    {
        return json_decode($this->getPersonJsonString(), true);
    }

    /**
     * @return string
     */
    private function getPersonJsonString(): string
    {
        return <<<JSON
{
    "name": "John Doe",
    "addresses": [
        {
            "city": "Boston"
        },
        {
            "city": "New York"
        }
    ]
}
JSON;
    }
}
