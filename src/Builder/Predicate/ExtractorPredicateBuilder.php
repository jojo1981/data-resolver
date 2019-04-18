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

/**
 * Not a predicate build but a man in the middle to force a correctly build predicate
 *
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
class ExtractorPredicateBuilder
{
    /** @var PredicateBuilderFactory */
    private $predicateBuilderFactory;

    /** @var ExtractorInterface */
    private $extractorBuilder;

    /**
     * @param PredicateBuilderFactory $predicateBuilderFactory
     * @param ExtractorBuilderInterface $extractorBuilder
     */
    public function __construct(
        PredicateBuilderFactory $predicateBuilderFactory,
        ExtractorBuilderInterface $extractorBuilder
    ) {
        $this->predicateBuilderFactory = $predicateBuilderFactory;
        $this->extractorBuilder = $extractorBuilder;
    }

    /**
     * @param mixed $expectedValue
     * @return ConditionalPredicateBuilder
     */
    public function equals($expectedValue): ConditionalPredicateBuilder
    {
        return $this->predicateBuilderFactory->getConditionalPredicateBuilder(
            $this->extractorBuilder,
            $this->predicateBuilderFactory->getEqualsPredicateBuilder($expectedValue)
        );
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ConditionalPredicateBuilder
     */
    public function some(PredicateBuilderInterface $predicateBuilder): ConditionalPredicateBuilder
    {
        return $this->predicateBuilderFactory->getConditionalPredicateBuilder(
            $this->extractorBuilder,
            $this->predicateBuilderFactory->getSomePredicateBuilder($predicateBuilder)
        );
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ConditionalPredicateBuilder
     */
    public function all(PredicateBuilderInterface $predicateBuilder): ConditionalPredicateBuilder
    {
        return $this->predicateBuilderFactory->getConditionalPredicateBuilder(
            $this->extractorBuilder,
            $this->predicateBuilderFactory->getAllPredicateBuilder($predicateBuilder)
        );
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ConditionalPredicateBuilder
     */
    public function none(PredicateBuilderInterface $predicateBuilder): ConditionalPredicateBuilder
    {
        return $this->predicateBuilderFactory->getConditionalPredicateBuilder(
            $this->extractorBuilder,
            $this->predicateBuilderFactory->getNonePredicateBuilder($predicateBuilder)
        );
    }
}