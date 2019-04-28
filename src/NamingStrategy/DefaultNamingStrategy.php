<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\NamingStrategy;

use Jojo1981\DataResolver\Helper\StringHelper;

/**
 * @package Jojo1981\DataResolver\NamingStrategy
 */
class DefaultNamingStrategy implements NamingStrategyInterface
{
    /**
     * @param string $propertyName
     * @return string[]
     */
    public function getPropertyNames(string $propertyName): array
    {
        return \array_values(\array_unique([
            StringHelper::toSnakeCase($propertyName),
            StringHelper::toCamelCase($propertyName)
        ]));
    }

    /**
     * @param string $propertyName
     * @return string[]
     */
    public function getMethodNames(string $propertyName): array
    {
        return ['get' . StringHelper::toCamelCase($propertyName, true)];
    }
}