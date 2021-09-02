<?php declare(strict_types=1);
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
use Jojo1981\DataResolver\Predicate\BooleanPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
class BooleanPredicateBuilder implements PredicateBuilderInterface
{
    /** @var bool */
    private bool $expected;

    /** @var bool */
    private bool $strict;

    /**
     * @param bool $expected
     * @param bool $strict
     */
    public function __construct(bool $expected, bool $strict)
    {
        $this->expected = $expected;
        $this->strict = $strict;
    }

    /**
     * @return BooleanPredicate
     */
    public function build(): PredicateInterface
    {
        return new BooleanPredicate($this->expected, $this->strict);
    }
}
