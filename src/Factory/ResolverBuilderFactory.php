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

use Jojo1981\DataResolver\Builder\Predicate\ExtractorPredicateBuilder;
use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Builder\ResolverBuilder;

/**
 * @api
 * @package Jojo1981\DataResolver\Factory
 */
class ResolverBuilderFactory
{
    /** @var ExtractorBuilderFactory */
    private $extractorBuilderFactory;

    /** @var PredicateBuilderFactory */
    private $predicateBuilderFactory;

    /**
     * @param ExtractorBuilderFactory $extractorBuilderFactory
     * @param PredicateBuilderFactory $predicateBuilderFactory
     */
    public function __construct(
        ExtractorBuilderFactory $extractorBuilderFactory,
        PredicateBuilderFactory $predicateBuilderFactory
    ) {
        $this->extractorBuilderFactory = $extractorBuilderFactory;
        $this->predicateBuilderFactory = $predicateBuilderFactory;
    }

    /**
     * @return ResolverBuilder
     */
    public function create(): ResolverBuilder
    {
        return new ResolverBuilder($this->extractorBuilderFactory);
    }

    /**
     * @param ResolverBuilder $resolverBuilder
     * @return ResolverBuilder
     */
    public function compose(ResolverBuilder $resolverBuilder): ResolverBuilder
    {
        return new ResolverBuilder($this->extractorBuilderFactory, $resolverBuilder);
    }

    /**
     * @param string $propertyName
     * @return ResolverBuilder
     */
    public function get(string $propertyName): ResolverBuilder
    {
        return $this->create()->get($propertyName);
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ResolverBuilder
     */
    public function filter(PredicateBuilderInterface $predicateBuilder): ResolverBuilder
    {
        return $this->create()->filter($predicateBuilder);
    }

    /**
     * @param ResolverBuilder $resolverBuilder
     * @return ResolverBuilder
     */
    public function flatten(ResolverBuilder $resolverBuilder): ResolverBuilder
    {
        return $this->create()->flatten($resolverBuilder);
    }

    /**
     * @param string $propertyName
     * @return ExtractorPredicateBuilder
     */
    public function where(string $propertyName): ExtractorPredicateBuilder
    {
        return $this->predicateBuilderFactory->getExtractorPredicateBuilder(
            $this->extractorBuilderFactory->getPropertyExtractorBuilder($propertyName)
        );
    }
}