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
use Jojo1981\DataResolver\Builder\Extractor\FilterExtractorBuilder;
use Jojo1981\DataResolver\Builder\Extractor\FindExtractorBuilder;
use Jojo1981\DataResolver\Builder\Extractor\FlattenExtractorBuilder;
use Jojo1981\DataResolver\Builder\Extractor\PropertyExtractorBuilder;
use Jojo1981\DataResolver\Builder\Extractor\ResolverExtractorBuilder;
use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Builder\ResolverBuilder;
use Jojo1981\DataResolver\Handler\MergeHandlerInterface;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;

/**
 * @internal
 * @package Jojo1981\DataResolver\Factory
 */
class ExtractorBuilderFactory
{
    /** @var NamingStrategyInterface */
    private $namingStrategy;

    /** @var PropertyHandlerInterface */
    private $propertyHandler;

    /** @var SequenceHandlerInterface */
    private $sequenceHandler;

    /** @var MergeHandlerInterface */
    private $mergeHandler;

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @param PropertyHandlerInterface $propertyHandler
     * @param SequenceHandlerInterface $sequenceHandler
     * @param MergeHandlerInterface $mergeHandler
     */
    public function __construct(
        NamingStrategyInterface $namingStrategy,
        PropertyHandlerInterface $propertyHandler,
        SequenceHandlerInterface $sequenceHandler,
        MergeHandlerInterface $mergeHandler
    ) {
        $this->namingStrategy = $namingStrategy;
        $this->propertyHandler = $propertyHandler;
        $this->sequenceHandler = $sequenceHandler;
        $this->mergeHandler = $mergeHandler;
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return FilterExtractorBuilder
     */
    public function getFilterExtractorBuilder(PredicateBuilderInterface $predicateBuilder): FilterExtractorBuilder
    {
        return new FilterExtractorBuilder($this->sequenceHandler, $predicateBuilder->build());
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return FindExtractorBuilder
     */
    public function getFindExtractorBuilder(PredicateBuilderInterface $predicateBuilder): FindExtractorBuilder
    {
        return new FindExtractorBuilder($this->sequenceHandler, $predicateBuilder->build());
    }

    /**
     * @param ExtractorBuilderInterface $extractorBuilder
     * @return FlattenExtractorBuilder
     */
    public function getFlattenExtractorBuilder(ExtractorBuilderInterface $extractorBuilder): FlattenExtractorBuilder
    {
        return new FlattenExtractorBuilder($this->sequenceHandler, $extractorBuilder->build());
    }

    /**
     * @param string $propertyName
     * @param string ...$propertyNames
     * @return PropertyExtractorBuilder
     */
    public function getPropertyExtractorBuilder(string $propertyName, string ...$propertyNames): PropertyExtractorBuilder
    {
        return new PropertyExtractorBuilder(
            $this->namingStrategy,
            $this->propertyHandler,
            $this->mergeHandler,
            \array_merge($propertyNames, [$propertyName])
        );
    }

    /**
     * @param ResolverBuilder $resolverBuilder
     * @return ResolverExtractorBuilder
     */
    public function getResolverExtractorBuilder(ResolverBuilder $resolverBuilder): ResolverExtractorBuilder
    {
        return new ResolverExtractorBuilder($resolverBuilder->build());
    }
}