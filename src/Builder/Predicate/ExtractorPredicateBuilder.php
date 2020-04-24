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
     * @param mixed $referenceValue
     * @return ConditionalPredicateBuilder
     */
    public function equals($referenceValue): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getEqualsPredicateBuilder($referenceValue)
        );
    }

    /**
     * @param mixed $referenceValue
     * @return ConditionalPredicateBuilder
     */
    public function greaterThan($referenceValue): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getGreaterThanPredicateBuilder($referenceValue)
        );
    }

    /**
     * @param mixed $referenceValue
     * @return ConditionalPredicateBuilder
     */
    public function lessThan($referenceValue): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getLessThanPredicateBuilder($referenceValue)
        );
    }

    /**
     * @param mixed $referenceValue
     * @return ConditionalPredicateBuilder
     */
    public function greaterThanOrEquals($referenceValue): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getOrPredicateBuilder(
                $this->predicateBuilderFactory->getGreaterThanPredicateBuilder($referenceValue),
                $this->predicateBuilderFactory->getEqualsPredicateBuilder($referenceValue)
            )
        );
    }

    /**
     * @param mixed $referenceValue
     * @return ConditionalPredicateBuilder
     */
    public function lessThanOrEquals($referenceValue): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getOrPredicateBuilder(
                $this->predicateBuilderFactory->getLessThanPredicateBuilder($referenceValue),
                $this->predicateBuilderFactory->getEqualsPredicateBuilder($referenceValue)
            )
        );
    }

    /**
     * @param mixed $referenceValue
     * @return ConditionalPredicateBuilder
     */
    public function notEquals($referenceValue): ConditionalPredicateBuilder
    {
        return $this->not($this->predicateBuilderFactory->getEqualsPredicateBuilder($referenceValue));
    }

    /**
     * @return ConditionalPredicateBuilder
     */
    public function isTrue(): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getBooleanPredicateBuilder(true, true)
        );
    }

    /**
     * @return ConditionalPredicateBuilder
     */
    public function isTruly(): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getBooleanPredicateBuilder(true, false)
        );
    }

    /**
     * @return ConditionalPredicateBuilder
     */
    public function isFalse(): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getBooleanPredicateBuilder(false, true)
        );
    }

    /**
     * @return ConditionalPredicateBuilder
     */
    public function isFalsely(): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getBooleanPredicateBuilder(false, false)
        );
    }

    /**
     * @return ConditionalPredicateBuilder
     */
    public function isNull(): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder($this->predicateBuilderFactory->getNullPredicateBuilder());
    }

    /**
     * @return ConditionalPredicateBuilder
     */
    public function isNotNull(): ConditionalPredicateBuilder
    {
        return $this->not($this->predicateBuilderFactory->getNullPredicateBuilder());
    }

    /**
     * @param callable $callback
     * @return ConditionalPredicateBuilder
     */
    public function callback(callable $callback): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getCallBackPredicateBuilder($callback)
        );
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ConditionalPredicateBuilder
     */
    public function not(PredicateBuilderInterface $predicateBuilder): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getNotPredicateBuilder($predicateBuilder)
        );
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ConditionalPredicateBuilder
     */
    public function some(PredicateBuilderInterface $predicateBuilder): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getSomePredicateBuilder($predicateBuilder)
        );
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ConditionalPredicateBuilder
     */
    public function all(PredicateBuilderInterface $predicateBuilder): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getAllPredicateBuilder($predicateBuilder)
        );
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ConditionalPredicateBuilder
     */
    public function none(PredicateBuilderInterface $predicateBuilder): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getNonePredicateBuilder($predicateBuilder)
        );
    }

    /**
     * @param mixed[] $expectedValues
     * @return ConditionalPredicateBuilder
     */
    public function in(array $expectedValues): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getInPredicateBuilder($expectedValues)
        );
    }

    /**
     * @param array $expectedValues
     * @return ConditionalPredicateBuilder
     */
    public function notIn(array $expectedValues): ConditionalPredicateBuilder
    {
        return $this->not($this->predicateBuilderFactory->getInPredicateBuilder($expectedValues));
    }

    /**
     * @param string $prefix
     * @param bool $caseSensitive
     * @return ConditionalPredicateBuilder
     */
    public function stringStartsWith(string $prefix, bool $caseSensitive = true): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getStringStartsWithPredicateBuilder($prefix, $caseSensitive)
        );
    }

    /**
     * @param string $suffix
     * @param bool $caseSensitive
     * @return ConditionalPredicateBuilder
     */
    public function stringEndsWith(string $suffix, bool $caseSensitive = true): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getStringEndsWithPredicateBuilder($suffix, $caseSensitive)
        );
    }

    /**
     * @param string $subString
     * @param bool $caseSensitive
     * @return ConditionalPredicateBuilder
     */
    public function stringContains(string $subString, bool $caseSensitive = true): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getStringContainsPredicateBuilder($subString, $caseSensitive)
        );
    }

    /**
     * @param string $pattern
     * @return ConditionalPredicateBuilder
     */
    public function stringMatchesRegex(string $pattern): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getStringRegexPredicateBuilder($pattern)
        );
    }

    /**
     * @param string $propertyName
     * @return ConditionalPredicateBuilder
     */
    public function hasProperty(string $propertyName): ConditionalPredicateBuilder
    {
        return $this->getConditionalPredicateBuilder(
            $this->predicateBuilderFactory->getHasPropertyPredicateBuilder($propertyName)
        );
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ConditionalPredicateBuilder
     */
    private function getConditionalPredicateBuilder(
        PredicateBuilderInterface $predicateBuilder
    ): ConditionalPredicateBuilder {
        return $this->predicateBuilderFactory->getConditionalPredicateBuilder(
            $this->extractorBuilder,
            $predicateBuilder
        );
    }
}
