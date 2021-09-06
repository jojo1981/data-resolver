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
use Jojo1981\DataResolver\Comparator\ComparatorInterface;
use Jojo1981\DataResolver\Predicate\EqualsPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
final class EqualsPredicateBuilder implements PredicateBuilderInterface
{
    /** @var ComparatorInterface */
    private ComparatorInterface $comparator;

    /** @var mixed */
    private $referenceValue;

    /**
     * @param mixed $referenceValue
     * @param ComparatorInterface $comparator
     */
    public function __construct(ComparatorInterface $comparator, $referenceValue)
    {
        $this->comparator = $comparator;
        $this->referenceValue = $referenceValue;
    }

    /**
     * @return EqualsPredicate
     */
    public function build(): PredicateInterface
    {
        return new EqualsPredicate($this->comparator, $this->referenceValue);
    }
}
