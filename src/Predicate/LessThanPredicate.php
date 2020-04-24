<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Comparator\ComparatorInterface;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @package Jojo1981\DataResolver\Predicate
 */
class LessThanPredicate implements PredicateInterface
{
    /** @var ComparatorInterface */
    private $comparator;

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
     * @param Context $context
     * @return bool
     */
    public function match(Context $context): bool
    {
        return $this->comparator->isLessThan($this->referenceValue, $context->getData());
    }
}
