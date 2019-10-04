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
 * Not a predicate builder but a man in the middle to force correctly building the predicate
 *
 * @api
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
     * @param mixed $expectedValue
     * @return ConditionalPredicateBuilder
     */
    public function notEquals($expectedValue): ConditionalPredicateBuilder
    {
        return $this->not($this->predicateBuilderFactory->getEqualsPredicateBuilder($expectedValue));
    }

    /**
     * @return ConditionalPredicateBuilder
     */
    public function isTrue(): ConditionalPredicateBuilder
    {
        return $this->equals(true);
    }

    /**
     * @return ConditionalPredicateBuilder
     */
    public function isFalse(): ConditionalPredicateBuilder
    {
        return $this->equals(false);
    }

    /**
     * @param callable $callback
     * @return ConditionalPredicateBuilder
     */
    public function callback(callable $callback): ConditionalPredicateBuilder
    {
        return $this->predicateBuilderFactory->getConditionalPredicateBuilder(
            $this->extractorBuilder,
            $this->predicateBuilderFactory->getCallBackPredicateBuilder($callback)
        );
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ConditionalPredicateBuilder
     */
    public function not(PredicateBuilderInterface $predicateBuilder): ConditionalPredicateBuilder
    {
        return $this->predicateBuilderFactory->getConditionalPredicateBuilder(
            $this->extractorBuilder,
            $this->predicateBuilderFactory->getNotPredicateBuilder($predicateBuilder)
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

    /**
     * @param mixed[] $expectedValues
     * @return ConditionalPredicateBuilder
     */
    public function in(array $expectedValues): ConditionalPredicateBuilder
    {
        return $this->predicateBuilderFactory->getConditionalPredicateBuilder(
            $this->extractorBuilder,
            $this->predicateBuilderFactory->getInPredicateBuilder($expectedValues)
        );
    }
}