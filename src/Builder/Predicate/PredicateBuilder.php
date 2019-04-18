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
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
class PredicateBuilder implements PredicateBuilderInterface
{
    /** @var PredicateBuilderInterface */
    private $predicate;

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     */
    public function __construct(PredicateBuilderInterface $predicateBuilder)
    {
        $this->predicate = $predicateBuilder;
    }

    /**
     * @return PredicateInterface
     */
    public function build(): PredicateInterface
    {
        return $this->predicate->build();
    }
}