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

use Jojo1981\DataResolver\Builder\ExtractorBuilderInterface;
use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Predicate\ExtractorPredicate;
use Jojo1981\DataResolver\Predicate\OrPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
class OrPredicateBuilder implements PredicateBuilderInterface
{
    /** @var PredicateBuilderInterface */
    private $leftPredicateBuilder;

    /** @var PredicateBuilderInterface */
    private $rightPredicateBuilder;

    /**
     * @param PredicateBuilderInterface $leftPredicateBuilder
     * @param PredicateBuilderInterface $rightPredicateBuilder
     */
    public function __construct(
        PredicateBuilderInterface $leftPredicateBuilder,
        PredicateBuilderInterface $rightPredicateBuilder
    ) {
        $this->leftPredicateBuilder = $leftPredicateBuilder;
        $this->rightPredicateBuilder = $rightPredicateBuilder;
    }

    /**
     * @return PredicateInterface
     */
    public function build(): PredicateInterface
    {
        return new OrPredicate(
            $this->leftPredicateBuilder->build(),
            $this->rightPredicateBuilder->build()
        );
    }
}