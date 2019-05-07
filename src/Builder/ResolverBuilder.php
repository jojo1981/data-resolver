<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Builder;

use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Factory\ExtractorBuilderFactory;
use Jojo1981\DataResolver\Resolver;

/**
 * @api
 * @package Jojo1981\DataResolver\Builder
 */
class ResolverBuilder
{
    /** @var ExtractorBuilderFactory */
    private $extractorBuilderFactory;

    /** @var ExtractorInterface[] */
    private $extractors = [];

    /**
     * @param ExtractorBuilderFactory $extractorBuilderFactory
     * @param null|ResolverBuilder $resolverBuilder
     */
    public function __construct(ExtractorBuilderFactory $extractorBuilderFactory, ?ResolverBuilder $resolverBuilder = null)
    {
        $this->extractorBuilderFactory = $extractorBuilderFactory;
        if (null !== $resolverBuilder) {
            $this->extractors[] = $extractorBuilderFactory->getResolverExtractorBuilder($resolverBuilder)->build();
        }
    }

    /**
     * @param string $propertyName
     * @return $this
     */
    public function get(string $propertyName): self
    {
        $this->extractors[] = $this->extractorBuilderFactory->getPropertyExtractorBuilder($propertyName)->build();

        return $this;
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return $this
     */
    public function find(PredicateBuilderInterface $predicateBuilder): self
    {
        $this->extractors[] = $this->extractorBuilderFactory->getFindExtractorBuilder($predicateBuilder)->build();

        return $this;
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return $this
     */
    public function filter(PredicateBuilderInterface $predicateBuilder): self
    {
        $this->extractors[] = $this->extractorBuilderFactory->getFilterExtractorBuilder($predicateBuilder)->build();

        return $this;
    }

    /**
     * @param ResolverBuilder $resolverBuilder
     * @return $this
     */
    public function flatten(ResolverBuilder $resolverBuilder): self
    {
        $this->extractors[] = $this->extractorBuilderFactory->getFlattenExtractorBuilder(
            $this->extractorBuilderFactory->getResolverExtractorBuilder($resolverBuilder)
        )->build();

        return $this;
    }

    /**
     * @return Resolver
     */
    public function build(): Resolver
    {
        return new Resolver($this->extractors);
    }
}