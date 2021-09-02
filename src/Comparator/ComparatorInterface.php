<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Comparator;

/**
 * @package Jojo1981\DataResolver\Comparator
 */
interface ComparatorInterface
{
    /**
     * @param mixed $referenceValue
     * @param mixed $toCompareValue
     * @return bool
     */
    public function isEqual($referenceValue, $toCompareValue): bool;

    /**
     * @param mixed $referenceValue
     * @param mixed $toCompareValue
     * @return bool
     */
    public function isGreaterThan($referenceValue, $toCompareValue): bool;

    /**
     * @param mixed $referenceValue
     * @param mixed $toCompareValue
     * @return bool
     */
    public function isLessThan($referenceValue, $toCompareValue): bool;
}
