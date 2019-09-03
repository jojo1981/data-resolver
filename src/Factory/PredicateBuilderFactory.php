<?php
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
use Jojo1981\DataResolver\Builder\Predicate\CallBackPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\ConditionalPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\EqualsPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\ExtractorPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\InPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\NonePredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\NotPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\OrPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\PredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\SomePredicateBuilder;
use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Comparator\ComparatorInterface;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;

/**
 * @internal
 * @package Jojo1981\DataResolver\Factory
 */
class PredicateBuilderFactory
{
    /** @var SequenceHandlerInterface */
    private $sequenceHandler;

    /** @var ComparatorInterface */
    private $comparator;

    /**
     * @param SequenceHandlerInterface $sequenceHandler
     * @param ComparatorInterface $comparator
     */
    public function __construct(SequenceHandlerInterface $sequenceHandler, ComparatorInterface $comparator)
    {
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
    ): AndPredicateBuilder
    {
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
    ): ConditionalPredicateBuilder
    {
        return new ConditionalPredicateBuilder($this, $extractorBuilder->build(), $predicateBuilder->build());
    }

    /**
     * @param mixed $expectedValue
     * @return EqualsPredicateBuilder
     */
    public function getEqualsPredicateBuilder($expectedValue): EqualsPredicateBuilder
    {
        return new EqualsPredicateBuilder($expectedValue, $this->comparator);
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
        return new ExtractorPredicateBuilder($this, $extractorBuilder);
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
    ): OrPredicateBuilder
    {
        return new OrPredicateBuilder($leftPredicateBuilder->build(), $rightPredicateBuilder->build());
    }

    /**
     * @param mixed[] $expectedValues
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
}