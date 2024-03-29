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

use Jojo1981\DataResolver\Builder\Predicate\AndPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\ExtractorPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\NotPredicateBuilder;
use Jojo1981\DataResolver\Builder\Predicate\OrPredicateBuilder;
use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Builder\ResolverBuilder;
use Jojo1981\DataResolver\Factory\Exception\FactoryException;
use Jojo1981\DataResolver\Resolver;
use function array_shift;
use function explode;
use function is_string;
use function sprintf;
use function strpos;
use function trim;

/**
 * @api
 * @package Jojo1981\DataResolver\Factory
 */
final class ResolverBuilderFactory
{
    /** @var ExtractorBuilderFactory */
    private ExtractorBuilderFactory $extractorBuilderFactory;

    /** @var PredicateBuilderFactory */
    private PredicateBuilderFactory $predicateBuilderFactory;

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
     * @param string ...$propertyNames
     * @return ResolverBuilder
     */
    public function get(string $propertyName, ...$propertyNames): ResolverBuilder
    {
        return $this->create()->get($propertyName, ...$propertyNames);
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
     * @param PredicateBuilderInterface $predicateBuilder
     * @return ResolverBuilder
     */
    public function find(PredicateBuilderInterface $predicateBuilder): ResolverBuilder
    {
        return $this->create()->find($predicateBuilder);
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return Resolver
     */
    public function all(PredicateBuilderInterface $predicateBuilder): Resolver
    {
        return $this->create()->all($predicateBuilder);
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return Resolver
     */
    public function none(PredicateBuilderInterface $predicateBuilder): Resolver
    {
        return $this->create()->none($predicateBuilder);
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return Resolver
     */
    public function some(PredicateBuilderInterface $predicateBuilder): Resolver
    {
        return $this->create()->some($predicateBuilder);
    }

    /**
     * @return Resolver
     */
    public function count(): Resolver
    {
        return $this->create()->count();
    }

    /**
     * @return Resolver
     */
    public function strlen(): Resolver
    {
        return $this->create()->strlen();
    }

    /**
     * @param string $propertyName
     * @return Resolver
     */
    public function hasProperty(string $propertyName): Resolver
    {
        return $this->create()->hasProperty($propertyName);
    }

    /**
     * @param string|ResolverBuilder|null $arg
     * @return ExtractorPredicateBuilder
     * @throws FactoryException
     */
    public function where($arg = null): ExtractorPredicateBuilder
    {
        if (null === $arg) {
            return $this->predicateBuilderFactory->getExtractorPredicateBuilder(
                $this->extractorBuilderFactory->getResolverExtractorBuilder($this->create())
            );
        }

        if (is_string($arg)) {
            return $this->getExtractorPredicateBuilderForPropertyName($arg);
        }

        if ($arg instanceof ResolverBuilder) {
            return $this->predicateBuilderFactory->getExtractorPredicateBuilder(
                $this->extractorBuilderFactory->getResolverExtractorBuilder($arg)
            );
        }

        throw new FactoryException(sprintf(
            'Invalid argument given for method `where`, should be of type string or an instance of %s',
            ResolverBuilder::class
        ));
    }

    /**
     * @param PredicateBuilderInterface $predicateBuilder
     * @return NotPredicateBuilder
     */
    public function not(PredicateBuilderInterface $predicateBuilder): NotPredicateBuilder
    {
        return $this->predicateBuilderFactory->getNotPredicateBuilder($predicateBuilder);
    }

    /**
     * @param PredicateBuilderInterface $leftPredicateBuilder
     * @param PredicateBuilderInterface $rightPredicateBuilder
     * @return OrPredicateBuilder
     */
    public function or(
        PredicateBuilderInterface $leftPredicateBuilder,
        PredicateBuilderInterface $rightPredicateBuilder
    ): OrPredicateBuilder {
        return $this->predicateBuilderFactory->getOrPredicateBuilder($leftPredicateBuilder, $rightPredicateBuilder);
    }

    /**
     * @param PredicateBuilderInterface $leftPredicateBuilder
     * @param PredicateBuilderInterface $rightPredicateBuilder
     * @return AndPredicateBuilder
     */
    public function and(
        PredicateBuilderInterface $leftPredicateBuilder,
        PredicateBuilderInterface $rightPredicateBuilder
    ): AndPredicateBuilder {
        return $this->predicateBuilderFactory->getAndPredicateBuilder($leftPredicateBuilder, $rightPredicateBuilder);
    }

    /**
     * @param string $propertyName
     * @return ExtractorPredicateBuilder
     */
    private function getExtractorPredicateBuilderForPropertyName(string $propertyName): ExtractorPredicateBuilder
    {
        $propertyName = trim($propertyName, '.');
        if (false === strpos($propertyName, '.')) {
            return $this->predicateBuilderFactory->getExtractorPredicateBuilder(
                $this->extractorBuilderFactory->getPropertyExtractorBuilder($propertyName)
            );
        }

        return $this->getExtractorPredicateBuilderForPropertyNames(explode('.', $propertyName));
    }

    /**
     * @param string[] $propertyNames
     * @return ExtractorPredicateBuilder
     */
    private function getExtractorPredicateBuilderForPropertyNames(array $propertyNames): ExtractorPredicateBuilder
    {
        $extractorBuilder = $this->extractorBuilderFactory->getCompositeExtractorBuilder(
            $this->extractorBuilderFactory->getPropertyExtractorBuilder(array_shift($propertyNames)),
            $this->extractorBuilderFactory->getPropertyExtractorBuilder(array_shift($propertyNames))
        );

        foreach ($propertyNames as $propertyName) {
            $extractorBuilder = $this->extractorBuilderFactory->getCompositeExtractorBuilder(
                $extractorBuilder,
                $this->extractorBuilderFactory->getPropertyExtractorBuilder($propertyName)
            );
        }

        return $this->predicateBuilderFactory->getExtractorPredicateBuilder($extractorBuilder);
    }
}
