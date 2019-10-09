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

use PHPUnit\Framework\Constraint\IsEqual;

/**
 * @package Jojo1981\DataResolver\Comparator
 */
class DefaultComparator implements ComparatorInterface
{
    /**
     * @param mixed $referenceValue
     * @param mixed $toCompareValue
     * @return bool
     */
    public function isEqual($referenceValue, $toCompareValue): bool
    {
        try {
            return (new IsEqual($referenceValue, 0.0, 25, true))->evaluate($toCompareValue, '', true);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param mixed $referenceValue
     * @param mixed $toCompareValue
     * @return bool
     */
    public function isGreaterThan($referenceValue, $toCompareValue): bool
    {
        return $toCompareValue > $referenceValue;
    }

    /**
     * @param mixed $referenceValue
     * @param mixed $toCompareValue
     * @return bool
     */
    public function isLessThan($referenceValue, $toCompareValue): bool
    {
        return $toCompareValue < $referenceValue;
    }
}