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
     * @param string ...$propertyNames
     * @return $this
     */
    public function get(string $propertyName, string ...$propertyNames): self
    {
        $this->extractors[] = $this->extractorBuilderFactory
            ->getPropertyExtractorBuilder($propertyName, ...$propertyNames)
            ->build();

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
     * @param PredicateBuilderInterface $predicateBuilder
     * @return Resolver
     */
    public function all(PredicateBuilderInterface $predicateBuilder): Resolver
    {
        $this->extractors[] = $this->extractorBuilderFactory->getAllExtractorBuilder($predicateBuilder)->build();

        return $this->build();
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return Resolver
     */
    public function none(PredicateBuilderInterface $predicateBuilder): Resolver
    {
        $this->extractors[] = $this->extractorBuilderFactory->getNoneExtractorBuilder($predicateBuilder)->build();

        return $this->build();
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return Resolver
     */
    public function some(PredicateBuilderInterface $predicateBuilder): Resolver
    {
        $this->extractors[] = $this->extractorBuilderFactory->getSomeExtractorBuilder($predicateBuilder)->build();

        return $this->build();
    }

    /**
     * @return Resolver
     */
    public function count(): Resolver
    {
        $this->extractors[] = $this->extractorBuilderFactory->getCountExtractorBuilder()->build();

        return $this->build();
    }

    /**
     * @return Resolver
     */
    public function strlen(): Resolver
    {
        $this->extractors[] = $this->extractorBuilderFactory->getStringLengthExtractorBuilder()->build();

        return $this->build();
    }

    /**
     * @param string $propertyName
     * @return Resolver
     */
    public function hasProperty(string $propertyName): Resolver
    {
        $this->extractors[] = $this->extractorBuilderFactory->getHasPropertyExtractorBuilder($propertyName)->build();

        return $this->build();
    }

    /**
     * @return Resolver
     */
    public function build(): Resolver
    {
        return new Resolver($this->extractors);
    }
}