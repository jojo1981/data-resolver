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
use Jojo1981\DataResolver\Comparator\ComparatorInterface;
use Jojo1981\DataResolver\Predicate\InPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
class InPredicateBuilder implements PredicateBuilderInterface
{
    /** @var mixed[] */
    private $expectedValues;

    /** @var ComparatorInterface */
    private $comparator;

    /**
     * @param mixed[] $expectedValues
     * @param ComparatorInterface $comparator
     */
    public function __construct(array $expectedValues, ComparatorInterface $comparator)
    {
        $this->expectedValues = $expectedValues;
        $this->comparator = $comparator;
    }

    /**
     * @return InPredicate
     */
    public function build(): PredicateInterface
    {
        return new InPredicate($this->expectedValues, $this->comparator);
    }
}