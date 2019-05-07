<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Handler;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;

/**
 * Property handlers can get data by the property name for the data it supports
 *
 * @package Jojo1981\DataResolver\Handler
 */
interface PropertyHandlerInterface
{
    /**
     * @param string $propertyName
     * @param mixed $data
     * @return bool
     */
    public function supports(string $propertyName, $data): bool;

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @param string $propertyName
     * @param mixed $data
     * @throws HandlerException
     * @return mixed
     */
    public function getValueForPropertyName(NamingStrategyInterface $namingStrategy, string $propertyName, $data);

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @param string $propertyName
     * @param mixed $data
     * @throws HandlerException
     * @return bool
     */
    public function hasValueForPropertyName(NamingStrategyInterface $namingStrategy, string $propertyName, $data): bool;
}