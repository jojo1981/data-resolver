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
    public function isEqual($referenceValue, $toCompareValue): bool
    {
        $comparator = $this->comparatorFactory->getComparatorFor($referenceValue, $toCompareValue);
        if (null === $comparator) {
            return false;
        }

        try {
            $comparator->assertEquals($referenceValue, $toCompareValue);
        } catch (ComparisonFailure $f) {
            return false;
        }

        return true;
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
