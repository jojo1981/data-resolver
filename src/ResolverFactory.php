<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver;

use Jojo1981\DataResolver\Builder\Predicate\ExtractorPredicateBuilder;
use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Builder\ResolverBuilder;
use Jojo1981\DataResolver\Factory\ExtractorBuilderFactory;
use Jojo1981\DataResolver\Factory\HandlerFactory;
use Jojo1981\DataResolver\Factory\PredicateBuilderFactory;

/**
 * @api
 * @package Jojo1981\DataResolver
 */
class ResolverFactory
{
    /** @var null|HandlerFactory */
    private $handlerFactory;

    /** @var ExtractorBuilderFactory */
    private $extractorBuilderFactory;

    /** @var PredicateBuilderFactory */
    private $predicateBuilderFactory;

    /**
     * @param HandlerFactory $handlerFactory
     * @return void
     */
    public function setHandlerFactory(HandlerFactory $handlerFactory): void
    {
        $this->handlerFactory = $handlerFactory;
    }

    /**
     * @return ResolverBuilder
     */
    public function create(): ResolverBuilder
    {
        return new ResolverBuilder($this->getExtractorBuilderFactory());
    }

    /**
     * @param ResolverBuilder $resolverBuilder
     * @return ResolverBuilder
     */
    public function compose(ResolverBuilder $resolverBuilder): ResolverBuilder
    {
        return new ResolverBuilder($this->getExtractorBuilderFactory(), $resolverBuilder);
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
        return $this->getPredicateBuilderFactory()->getExtractorPredicateBuilder(
            $this->getExtractorBuilderFactory()->getPropertyExtractorBuilder($propertyName)
        );
    }

    /**
     * @lazy
     * @return ExtractorBuilderFactory
     */
    private function getExtractorBuilderFactory(): ExtractorBuilderFactory
    {
        if (null === $this->extractorBuilderFactory) {
            $this->extractorBuilderFactory = new ExtractorBuilderFactory(
                $this->getHandlerFactory()->getPropertyHandler(),
                $this->getHandlerFactory()->getSequenceHandler()
            );
        }

        return $this->extractorBuilderFactory;
    }

    /**
     * @return PredicateBuilderFactory
     */
    private function getPredicateBuilderFactory(): PredicateBuilderFactory
    {
        if (null === $this->predicateBuilderFactory) {
            $this->predicateBuilderFactory = new PredicateBuilderFactory(
                $this->getHandlerFactory()->getSequenceHandler()
            );
        }

        return $this->predicateBuilderFactory;
    }

    /**
     * @lazy
     * @return HandlerFactory
     */
    private function getHandlerFactory(): HandlerFactory
    {
        if (null === $this->handlerFactory) {
            $this->handlerFactory = new HandlerFactory();
        }

        return $this->handlerFactory;
    }
}