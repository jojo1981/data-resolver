<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Factory;

use Jojo1981\DataResolver\Builder\ExtractorBuilderInterface;
use Jojo1981\DataResolver\Builder\Predicate\AllPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\AndPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\BooleanPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\CallBackPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\ConditionalPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\CountPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\EqualsPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\ExtractorPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\GreaterThanPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\HasPropertyPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\InPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\IsEmptyPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\LessThanPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\NonePredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\NotPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\NullPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\OrPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\PredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\SomePredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\StringContainsPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\StringEndsWithPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\StringRegexPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\StringStartsWithPredicateBuilder;
use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Comparator\ComparatorInterface;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;

/**
 * @internal
 * @package Jojo1981\DataResolver\Factory
 */
class PredicateBuilderFactory
{
    /** @var ExtractorBuilderFactory */
    private ExtractorBuilderFactory $extractorBuilderFactory;

    /** @var SequenceHandlerInterface */
    private SequenceHandlerInterface $sequenceHandler;

    /** @var ComparatorInterface */
    private ComparatorInterface $comparator;

    /**
     * @param ExtractorBuilderFactory $extractorBuilderFactory
     * @param SequenceHandlerInterface $sequenceHandler
     * @param ComparatorInterface $comparator
     */
    public function __construct(
        ExtractorBuilderFactory $extractorBuilderFactory,
        SequenceHandlerInterface $sequenceHandler,
        ComparatorInterface $comparator
    ) {
        $this->extractorBuilderFactory = $extractorBuilderFactory;
        $this->sequenceHandler = $sequenceHandler;
        $this->comparator = $comparator;
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return AllPredicateBuilder
     */
    public function getAllPredicateBuilder(PredicateBuilderInterface $predicateBuilder): AllPredicateBuilder
    {
        return new AllPredicateBuilder($this->sequenceHandler, $predicateBuilder->build());
    }

    /**
     * @param PredicateBuilderInterface $leftPredicateBuilder
     * @param PredicateBuilderInterface $rightPredicateBuilder
     * @return AndPredicateBuilder
     */
    public function getAndPredicateBuilder(
        PredicateBuilderInterface $leftPredicateBuilder,
        PredicateBuilderInterface $rightPredicateBuilder
    ): AndPredicateBuilder {
        return new AndPredicateBuilder($leftPredicateBuilder->build(), $rightPredicateBuilder->build());
    }

    /**
     * @param ExtractorBuilderInterface $extractorBuilder
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ConditionalPredicateBuilder
     */
    public function getConditionalPredicateBuilder(
        ExtractorBuilderInterface $extractorBuilder,
        PredicateBuilderInterface $predicateBuilder
    ): ConditionalPredicateBuilder {
        return new ConditionalPredicateBuilder($this, $extractorBuilder->build(), $predicateBuilder->build());
    }

    /**
     * @param mixed $referenceValue
     * @return EqualsPredicateBuilder
     */
    public function getEqualsPredicateBuilder($referenceValue): EqualsPredicateBuilder
    {
        return new EqualsPredicateBuilder($this->comparator, $referenceValue);
    }

    /**
     * @param mixed $referenceValue
     * @return GreaterThanPredicateBuilder
     */
    public function getGreaterThanPredicateBuilder($referenceValue): GreaterThanPredicateBuilder
    {
        return new GreaterThanPredicateBuilder($this->comparator, $referenceValue);
    }

    /**
     * @param mixed $referenceValue
     * @return LessThanPredicateBuilder
     */
    public function getLessThanPredicateBuilder($referenceValue): LessThanPredicateBuilder
    {
        return new LessThanPredicateBuilder($this->comparator, $referenceValue);
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return NotPredicateBuilder
     */
    public function getNotPredicateBuilder(PredicateBuilderInterface $predicateBuilder): NotPredicateBuilder
    {
        return new NotPredicateBuilder($predicateBuilder->build());
    }

    /**
     * @param callable $callback
     * @return CallBackPredicateBuilder
     */
    public function getCallBackPredicateBuilder(callable $callback): CallBackPredicateBuilder
    {
        return new CallBackPredicateBuilder($callback);
    }

    /**
     * @param ExtractorBuilderInterface $extractorBuilder
     * @return ExtractorPredicateBuilder
     */
    public function getExtractorPredicateBuilder(ExtractorBuilderInterface $extractorBuilder): ExtractorPredicateBuilder
    {
        return new ExtractorPredicateBuilder($this, $this->extractorBuilderFactory, $extractorBuilder);
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return NonePredicateBuilder
     */
    public function getNonePredicateBuilder(PredicateBuilderInterface $predicateBuilder): NonePredicateBuilder
    {
        return new NonePredicateBuilder($this->sequenceHandler, $predicateBuilder->build());
    }

    /**
     * @param PredicateBuilderInterface $leftPredicateBuilder
     * @param PredicateBuilderInterface $rightPredicateBuilder
     * @return OrPredicateBuilder
     */
    public function getOrPredicateBuilder(
        PredicateBuilderInterface $leftPredicateBuilder,
        PredicateBuilderInterface $rightPredicateBuilder
    ): OrPredicateBuilder {
        return new OrPredicateBuilder($leftPredicateBuilder->build(), $rightPredicateBuilder->build());
    }

    /**
     * @param array $expectedValues
     * @return InPredicateBuilder
     */
    public function getInPredicateBuilder(array $expectedValues): InPredicateBuilder
    {
        return new InPredicateBuilder($expectedValues, $this->comparator);
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return PredicateBuilder
     */
    public function getPredicateBuilder(PredicateBuilderInterface $predicateBuilder): PredicateBuilder
    {
        return new PredicateBuilder($predicateBuilder->build());
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return SomePredicateBuilder
     */
    public function getSomePredicateBuilder(PredicateBuilderInterface $predicateBuilder): SomePredicateBuilder
    {
        return new SomePredicateBuilder($this->sequenceHandler, $predicateBuilder->build());
    }

    /**
     * @param string $prefix
     * @param bool $caseSensitive
     * @return StringStartsWithPredicateBuilder
     */
    public function getStringStartsWithPredicateBuilder(
        string $prefix,
        bool $caseSensitive
    ): StringStartsWithPredicateBuilder {
        return new StringStartsWithPredicateBuilder($prefix, $caseSensitive);
    }

    /**
     * @param string $suffix
     * @param bool $caseSensitive
     * @return StringEndsWithPredicateBuilder
     */
    public function getStringEndsWithPredicateBuilder(
        string $suffix,
        bool $caseSensitive
    ): StringEndsWithPredicateBuilder {
        return new StringEndsWithPredicateBuilder($suffix, $caseSensitive);
    }

    /**
     * @param string $subString
     * @param bool $caseSensitive
     * @return StringContainsPredicateBuilder
     */
    public function getStringContainsPredicateBuilder(
        string $subString,
        bool $caseSensitive
    ): StringContainsPredicateBuilder {
        return new StringContainsPredicateBuilder($subString, $caseSensitive);
    }

    /**
     * @param string $pattern
     * @return StringRegexPredicateBuilder
     */
    public function getStringRegexPredicateBuilder(string $pattern): StringRegexPredicateBuilder
    {
        return new StringRegexPredicateBuilder($pattern);
    }

    /**
     * @param string $propertyName
     * @return HasPropertyPredicateBuilder
     */
    public function getHasPropertyPredicateBuilder(string $propertyName): HasPropertyPredicateBuilder
    {
        return new HasPropertyPredicateBuilder(
            $this->extractorBuilderFactory->getHasPropertyExtractorBuilder($propertyName)->build()
        );
    }

    /**
     * @param bool $expected
     * @param bool $strict
     * @return BooleanPredicateBuilder
     */
    public function getBooleanPredicateBuilder(bool $expected, bool $strict): BooleanPredicateBuilder
    {
        return new BooleanPredicateBuilder($expected, $strict);
    }

    /**
     * @return NullPredicateBuilder
     */
    public function getNullPredicateBuilder(): NullPredicateBuilder
    {
        return new NullPredicateBuilder();
    }

    /**
     * @return IsEmptyPredicateBuilder
     */
    public function getIsEmptyPredicateBuilder(): IsEmptyPredicateBuilder
    {
        return new IsEmptyPredicateBuilder($this->sequenceHandler);
    }

    /**
     * @param int $expectedCount
     * @return CountPredicateBuilder
     */
    public function getCountPredicateBuilder(int $expectedCount): CountPredicateBuilder
    {
        return new CountPredicateBuilder($this->sequenceHandler, $expectedCount);
    }
}
