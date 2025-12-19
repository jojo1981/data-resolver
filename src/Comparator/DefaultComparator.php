<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
declare(strict_types=1);

namespace Jojo1981\DataResolver\Comparator;

use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;
use SebastianBergmann\Comparator\RuntimeException;

/**
 * @package Jojo1981\DataResolver\Comparator
 */
final class DefaultComparator implements ComparatorInterface
{
    /** @var ComparatorFactory */
    private ComparatorFactory $comparatorFactory;

    /**
     * @param ComparatorFactory|null $comparatorFactory
     */
    public function __construct(?ComparatorFactory $comparatorFactory = null)
    {
        $this->comparatorFactory = $comparatorFactory ?? ComparatorFactory::getInstance();
    }

    /**
     * @param mixed $referenceValue
     * @param mixed $toCompareValue
     * @return bool
     * @throws RuntimeException
     * @throws RuntimeException
     */
    public function isEqual(mixed $referenceValue, mixed $toCompareValue): bool
    {
        $comparator = $this->comparatorFactory->getComparatorFor($referenceValue, $toCompareValue);
        try {
            $comparator->assertEquals($referenceValue, $toCompareValue);
        } catch (ComparisonFailure) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $referenceValue
     * @param mixed $toCompareValue
     * @return bool
     */
    public function isGreaterThan(mixed $referenceValue, mixed $toCompareValue): bool
    {
        return $toCompareValue > $referenceValue;
    }

    /**
     * @param mixed $referenceValue
     * @param mixed $toCompareValue
     * @return bool
     */
    public function isLessThan(mixed $referenceValue, mixed $toCompareValue): bool
    {
        return $toCompareValue < $referenceValue;
    }
}
