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
class InPredicate implements PredicateInterface
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
     * @param Context $context
     * @return bool
     */
    public function match(Context $context): bool
    {
        foreach ($this->expectedValues as $expectedValue) {
            if ($this->comparator->isEqual($expectedValue, $context->getData())) {
                return true;
            }
        }

        return false;
    }
}