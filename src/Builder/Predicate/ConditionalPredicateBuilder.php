<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Builder\Predicate;

use Jojo1981\DataResolver\Builder\ExtractorBuilderInterface;
use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Factory\PredicateBuilderFactory;
use Jojo1981\DataResolver\Predicate\ExtractorPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
class ConditionalPredicateBuilder implements PredicateBuilderInterface
{
    /** @var PredicateBuilderFactory */
    private $predicateBuilderFactory;

    /** @var ExtractorInterface */
    private $extractor;

    /** @var PredicateInterface */
    private $predicate;

    /**
     * @param PredicateBuilderFactory $predicateBuilderFactory
     * @param ExtractorInterface $extractor
     * @param PredicateInterface $predicate
     */
    public function __construct(
        PredicateBuilderFactory $predicateBuilderFactory,
        ExtractorInterface $extractor,
        PredicateInterface $predicate
    ) {
        $this->predicateBuilderFactory = $predicateBuilderFactory;
        $this->extractor = $extractor;
        $this->predicate = $predicate;
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return PredicateBuilder
     */
    public function and(PredicateBuilderInterface $predicateBuilder): PredicateBuilder
    {
        return $this->predicateBuilderFactory->getPredicateBuilder(
            $this->predicateBuilderFactory->getAndPredicateBuilder(
                $this,
                $predicateBuilder
            )
        );
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return PredicateBuilder
     */
    public function or(PredicateBuilderInterface $predicateBuilder): PredicateBuilder
    {
        return $this->predicateBuilderFactory->getPredicateBuilder(
            $this->predicateBuilderFactory->getOrPredicateBuilder(
                $this,
                $predicateBuilder
            )
        );
    }

    /**
     * @return PredicateInterface
     */
    public function build(): PredicateInterface
    {
        return new ExtractorPredicate($this->extractor, $this->predicate);
    }
}