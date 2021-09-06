<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2021 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Integration\Resolver;

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use tests\Jojo1981\DataResolver\Integration\AbstractIntegrationTestCase;

/**
 * @package tests\Jojo1981\DataResolver\Integration\Resolver
 */
final class FlattenTest extends AbstractIntegrationTestCase
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
    public function checkFlatten(): void
    {
        $resolver = $this->getResolverBuilderFactory()->create()
            ->flatten($this->getResolverBuilderFactory()->get('name'))
            ->build();

        self::assertEquals(['John Doe', 'Jane Doe'], $resolver->resolve($this->getTestData()));

    }

    /**
     * @return array[]
     */
    private function getTestData(): array
    {
        return [
            ['name' => 'John Doe'],
            ['name' => 'Jane Doe']
        ];
    }
}
