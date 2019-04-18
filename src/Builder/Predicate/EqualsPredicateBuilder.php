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
use Jojo1981\DataResolver\Predicate\EqualsPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
class EqualsPredicateBuilder implements PredicateBuilderInterface
{
    /** @var mixed */
    private $expectedValue;

    /**
     * @param mixed $expectedValue
     */
    public function __construct($expectedValue)
    {
        $this->expectedValue = $expectedValue;
    }

    /**
     * @return PredicateInterface
     */
    public function build(): PredicateInterface
    {
        return new EqualsPredicate($this->expectedValue);
    }
}