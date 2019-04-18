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

use Jojo1981\DataResolver\Builder\Extractor\ResolverExtractorBuilder;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Factory\ExtractorBuilderFactory;
use Jojo1981\DataResolver\Resolver;

/**
 * @package Jojo1981\DataResolver\Builder
 */
class ResolverBuilder
{
    /** @var ExtractorBuilderFactory */
    private $extractorBuilderFactory;

    /** @var ExtractorBuilderInterface[] */
    private $extractorBuilders = [];

    /**
     * @param ExtractorBuilderFactory $extractorBuilderFactory
     * @param null|ResolverBuilder $resolverBuilder
     */
    public function __construct(ExtractorBuilderFactory $extractorBuilderFactory, ?ResolverBuilder $resolverBuilder = null)
    {
        $this->extractorBuilderFactory = $extractorBuilderFactory;
        if (null !== $resolverBuilder) {
            $this->extractorBuilders[] = new ResolverExtractorBuilder($resolverBuilder);
        }
    }

    /**
     * @param string $propertyName
     * @return $this
     */
    public function get(string $propertyName): self
    {
        $this->extractorBuilders[] = $this->extractorBuilderFactory->getPropertyExtractorBuilder($propertyName);

        return $this;
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return $this
     */
    public function find(PredicateBuilderInterface $predicateBuilder): self
    {
        $this->extractorBuilders[] = $this->extractorBuilderFactory->getFindExtractorBuilder($predicateBuilder);

        return $this;
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return $this
     */
    public function filter(PredicateBuilderInterface $predicateBuilder): self
    {
        $this->extractorBuilders[] = $this->extractorBuilderFactory->getFilterExtractorBuilder($predicateBuilder);

        return $this;
    }

    /**
     * @param ResolverBuilder $resolverBuilder
     * @return $this
     */
    public function flatten(ResolverBuilder $resolverBuilder): self
    {
        $this->extractorBuilders[] = $this->extractorBuilderFactory->getFlattenExtractorBuilder(
            new ResolverExtractorBuilder($resolverBuilder)
        );

        return $this;
    }

    /**
     * @return Resolver
     */
    public function build(): Resolver
    {
        return new Resolver(\array_map(
            static function (ExtractorBuilderInterface $extractorBuilder): ExtractorInterface {
                return $extractorBuilder->build();
            },
            $this->extractorBuilders
        ));
    }
}