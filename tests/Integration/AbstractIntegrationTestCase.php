<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Integration;

use Jojo1981\DataResolver\Factory;
use Jojo1981\DataResolver\Factory\ResolverBuilderFactory;
use tests\Jojo1981\DataResolver\TestCase;

/**
 * @package tests\Jojo1981\DataResolver\Integration
 */
abstract class AbstractIntegrationTestCase extends TestCase
{
    /** @var ResolverBuilderFactory */
    private $resolverBuilderFactory;

    /**
     * @return ResolverBuilderFactory
     */
    final protected function getResolverBuilderFactory(): ResolverBuilderFactory
    {
        if (null === $this->resolverBuilderFactory) {
            $this->resolverBuilderFactory = (new Factory())->getResolverBuilderFactory();
        }

        return $this->resolverBuilderFactory;
    }
}