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
use Jojo1981\DataResolver\Predicate\InPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
class InPredicateBuilder implements PredicateBuilderInterface
{
    /** @var mixed[] */
    private $expectedValues;

    /**
     * @param mixed[] $expectedValues
     */
    public function __construct(array $expectedValues)
    {
        $this->expectedValues = $expectedValues;
    }

    /**
     * @return PredicateInterface
     */
    public function build(): PredicateInterface
    {
        return new InPredicate($this->expectedValues);
    }
}