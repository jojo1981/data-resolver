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

    /** @var ExtractorBuilderInterface */
    private $extractorBuilder;

    /** @var PredicateBuilderInterface */
    private $predicateBuilder;

    /**
     * @param PredicateBuilderFactory $predicateBuilderFactory
     * @param ExtractorBuilderInterface $extractorBuilder
     * @param PredicateBuilderInterface $predicateBuilder
     */
    public function __construct(
        PredicateBuilderFactory $predicateBuilderFactory,
        ExtractorBuilderInterface $extractorBuilder,
        PredicateBuilderInterface $predicateBuilder
    ) {
        $this->predicateBuilderFactory = $predicateBuilderFactory;
        $this->extractorBuilder = $extractorBuilder;
        $this->predicateBuilder = $predicateBuilder;
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return PredicateBuilder
     */
    public function and(PredicateBuilderInterface $predicateBuilder): PredicateBuilder
    {
        return $this->predicateBuilderFactory->getPredicateBuilder(
            $this->predicateBuilderFactory->getAndPredicateBuilder(
                $this->extractorBuilder,
                $this->predicateBuilder,
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
                $this->extractorBuilder,
                $this->predicateBuilder,
                $predicateBuilder
            )
        );
    }

    /**
     * @return PredicateInterface
     */
    public function build(): PredicateInterface
    {
        return new ExtractorPredicate(
            $this->extractorBuilder->build(),
            $this->predicateBuilder->build()
        );
    }
}