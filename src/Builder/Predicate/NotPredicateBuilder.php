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
use Jojo1981\DataResolver\Predicate\NotPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @api
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
final class NotPredicateBuilder implements PredicateBuilderInterface
{
    /** @var PredicateInterface */
    private PredicateInterface $predicate;

    /**
     * @param PredicateInterface $predicate
     */
    public function __construct(PredicateInterface $predicate)
    {
        $this->predicate = $predicate;
    }

    /**
     * @return NotPredicate
     */
    public function build(): PredicateInterface
    {
        return new NotPredicate($this->predicate);
    }
}
