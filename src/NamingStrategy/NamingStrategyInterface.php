<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\NamingStrategy;

/**
 * @package Jojo1981\DataResolver\NamingStrategy
 */
interface NamingStrategyInterface
{
    /**
     * @param string $propertyName
     * @return string[]
     */
    public function getPropertyNames(string $propertyName): array;

    /**
     * @param string $propertyName
     * @return string[]
     */
    public function getMethodNames(string $propertyName): array;
}
