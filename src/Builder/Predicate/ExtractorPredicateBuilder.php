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
use Jojo1981\DataResolver\Factory\ExtractorBuilderFactory;
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

    /** @var ExtractorBuilderFactory */
    private $extractorBuilderFactory;

    /** @var ExtractorBuilderInterface */
    private $extractorBuilder;

    /**
     * @param PredicateBuilderFactory $predicateBuilderFactory
     * @param ExtractorBuilderFactory $extractorBuilderFactory
     * @param ExtractorBuilderInterface $extractorBuilder
     */
    public function __construct(
        PredicateBuilderFactory $predicateBuilderFactory,
        ExtractorBuilderFactory $extractorBuilderFactory,
        ExtractorBuilderInterface $extractorBuilder
    ) {
        $this->predicateBuilderFactory = $predicateBuilderFactory;
        $this->extractorBuilderFactory = $extractorBuilderFactory;
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
     * @param string $propertyName
     * @return ExtractorPredicateBuilder
     */
    public function get(string $propertyName): ExtractorPredicateBuilder
    {
        return $this->predicateBuilderFactory->getExtractorPredicateBuilder(
            $this->extractorBuilderFactory->getCompositeExtractorBuilder(
                $this->extractorBuilder,
                $this->extractorBuilderFactory->getPropertyExtractorBuilder($propertyName)
            )
        );
    }

    /**
     * @param mixed $expectedValue
     * @return ConditionalPredicateBuilder
     */
    public function notEquals($expectedValue): ConditionalPredicateBuilder
    {
        return $this->not($this->equals($expectedValue));
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
     * @return ConditionalPredicateBuilder
     */
    public function isNull(): ConditionalPredicateBuilder
    {
        return $this->equals(null);
    }

    /**
     * @return ConditionalPredicateBuilder
     */
    public function isNotNull(): ConditionalPredicateBuilder
    {
        return $this->not($this->isNull());
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