<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Comparator;

use PHPUnit\Framework\Constraint\IsEqual as IsEqualConstraint;

/**
 * @package Jojo1981\DataResolver\Comparator
 */
class DefaultComparator implements ComparatorInterface
{
    /**
     * @param mixed $valueA
     * @param mixed $valueB
     * @return bool
     */
    public function isEqual($valueA, $valueB): bool
    {
        $isEqualConstraint = new IsEqualConstraint($valueA, 0.0, 25, true);
        try {
            return $isEqualConstraint->evaluate($valueB, '', true);
        } catch (\Exception $exception) {
            return false;
        }
    }
}