<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Builder\Predicate;

use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use Jojo1981\DataResolver\Predicate\HasPropertyPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
class HasPropertyPredicateBuilder implements PredicateBuilderInterface
{
    /** @var PropertyHandlerInterface */
    private $propertyHandler;

    /** @var NamingStrategyInterface */
    private $namingStrategy;

    /** @var string */
    private $propertyName;

    /**
     * @param PropertyHandlerInterface $propertyHandler
     * @param NamingStrategyInterface $namingStrategy
     * @param string $propertyName
     */
    public function __construct(
        PropertyHandlerInterface $propertyHandler,
        NamingStrategyInterface $namingStrategy,
        string $propertyName
    ) {
        $this->propertyHandler = $propertyHandler;
        $this->namingStrategy = $namingStrategy;
        $this->propertyName = $propertyName;
    }

    /**
     * @return PredicateInterface
     */
    public function build(): PredicateInterface
    {
        return new HasPropertyPredicate($this->propertyHandler, $this->namingStrategy, $this->propertyName);
    }
}