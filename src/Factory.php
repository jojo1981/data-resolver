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

use Jojo1981\DataResolver\Comparator\ComparatorInterface;
use Jojo1981\DataResolver\Comparator\DefaultComparator;
use Jojo1981\DataResolver\Exception\ResolverException;
use Jojo1981\DataResolver\Factory\ExtractorBuilderFactory;
use Jojo1981\DataResolver\Factory\PredicateBuilderFactory;
use Jojo1981\DataResolver\Factory\ResolverBuilderFactory;
use Jojo1981\DataResolver\Handler\MergeHandler\DefaultMergeHandler;
use Jojo1981\DataResolver\Handler\MergeHandlerInterface;
use Jojo1981\DataResolver\Handler\PropertyHandler\AssociativeArrayPropertyHandler;
use Jojo1981\DataResolver\Handler\PropertyHandler\CompositePropertyHandler;
use Jojo1981\DataResolver\Handler\PropertyHandler\ObjectPropertyHandler;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\Handler\SequenceHandler\ArraySequenceHandler;
use Jojo1981\DataResolver\Handler\SequenceHandler\CompositeSequenceHandler;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\DefaultNamingStrategy;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use function array_push;

/**
 * @api
 * @package Jojo1981\DataResolver
 */
class Factory
{
    /** @var ExtractorBuilderFactory */
    private $extractorBuilderFactory;

    /** @var PredicateBuilderFactory */
    private $predicateBuilderFactory;

    /** @var ResolverBuilderFactory */
    private $resolverBuilderFactory;

    /** @var PropertyHandlerInterface */
    private $propertyHandler;

    /** @var SequenceHandlerInterface */
    private $sequenceHandler;

    /** @var MergeHandlerInterface */
    private $mergeHandler;

    /** @var ComparatorInterface */
    private $comparator;

    /** @var bool */
    private $isFrozen = false;

    /** @var NamingStrategyInterface */
    private $namingStrategy;

    /** @var PropertyHandlerInterface[] */
    private $propertyHandlers = [];

    /** @var SequenceHandlerInterface[] */
    private $sequenceHandlers = [];

    /**
     * @return $this
     * @throws ResolverException
     */
    public function useDefaultPropertyHandlers(): self
    {
        $this->assertNotFrozen();
        foreach ($this->getDefaultPropertyHandlers() as $propertyHandler) {
            $this->registerPropertyHandler($propertyHandler);
        }

        return $this;
    }

    /**
     * @return $this
     * @throws ResolverException
     */
    public function useDefaultSequenceHandlers(): self
    {
        $this->assertNotFrozen();
        foreach ($this->getDefaultSequenceHandlers() as $sequenceHandler) {
            $this->registerSequenceHandler($sequenceHandler);
        }

        return $this;
    }

    /**
     * @param PropertyHandlerInterface $propertyHandler
     * @return $this
     * @throws ResolverException
     */
    public function registerPropertyHandler(PropertyHandlerInterface $propertyHandler): self
    {
        $this->assertNotFrozen();
        if (!\in_array($propertyHandler, $this->propertyHandlers, true)) {
            $this->propertyHandlers[] = $propertyHandler;
        }

        return $this;
    }

    /**
     * @param SequenceHandlerInterface $sequenceHandler
     * @return $this
     * @throws ResolverException
     */
    public function registerSequenceHandler(SequenceHandlerInterface $sequenceHandler): self
    {
        $this->assertNotFrozen();
        if (!\in_array($sequenceHandler, $this->sequenceHandlers, true)) {
            $this->sequenceHandlers[] = $sequenceHandler;
        }

        return $this;
    }

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @return $this
     * @throws ResolverException
     */
    public function setNamingStrategy(NamingStrategyInterface $namingStrategy): self
    {
        $this->assertNotFrozen();
        $this->namingStrategy = $namingStrategy;

        return $this;
    }

    /**
     * @param MergeHandlerInterface $mergeHandler
     * @return $this
     * @throws ResolverException
     */
    public function setMergeHandler(MergeHandlerInterface $mergeHandler): self
    {
        $this->assertNotFrozen();
        $this->mergeHandler = $mergeHandler;

        return $this;
    }

    /**
     * @param ComparatorInterface $comparator
     * @return $this
     * @throws ResolverException
     */
    public function setComparator(ComparatorInterface $comparator): self
    {
        $this->assertNotFrozen();
        $this->comparator = $comparator;

        return $this;
    }

    /**
     * @return ResolverBuilderFactory
     */
    public function getResolverBuilderFactory(): ResolverBuilderFactory
    {
        if (null === $this->resolverBuilderFactory) {
            $this->isFrozen = true;
            $this->resolverBuilderFactory = new ResolverBuilderFactory(
                $this->getExtractorBuilderFactory(),
                $this->getPredicateBuilderFactory()
            );
        }

        return $this->resolverBuilderFactory;
    }

    /**
     * @return ExtractorBuilderFactory
     */
    private function getExtractorBuilderFactory(): ExtractorBuilderFactory
    {
        if (null === $this->extractorBuilderFactory) {
            $this->extractorBuilderFactory = new ExtractorBuilderFactory(
                $this->getNamingStrategy(),
                $this->getPropertyHandler(),
                $this->getSequenceHandler(),
                $this->getMergeHandler()
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
                $this->getExtractorBuilderFactory(),
                $this->getSequenceHandler(),
                $this->getComparator()
            );
        }

        return $this->predicateBuilderFactory;
    }

    /**
     * @return PropertyHandlerInterface
     */
    private function getPropertyHandler(): PropertyHandlerInterface
    {
        if (null === $this->propertyHandler) {
            if (empty($this->propertyHandlers)) {
                array_push($this->propertyHandlers, ...$this->getDefaultPropertyHandlers());
            }
            $this->propertyHandler = new CompositePropertyHandler($this->propertyHandlers);
        }

        return $this->propertyHandler;
    }

    /**
     * @return SequenceHandlerInterface
     */
    private function getSequenceHandler(): SequenceHandlerInterface
    {
        if (null === $this->sequenceHandler) {
            if (empty($this->sequenceHandlers)) {
                array_push($this->sequenceHandlers, ...$this->getDefaultSequenceHandlers());
            }
            $this->sequenceHandler = new CompositeSequenceHandler($this->sequenceHandlers);
        }

        return $this->sequenceHandler;
    }

    /**
     * @return PropertyHandlerInterface[]
     */
    private function getDefaultPropertyHandlers(): array
    {
        return [new ObjectPropertyHandler(), new AssociativeArrayPropertyHandler()];
    }

    /**
     * @return SequenceHandlerInterface[]
     */
    private function getDefaultSequenceHandlers(): array
    {
        return [new ArraySequenceHandler()];
    }

    /**
     * @return NamingStrategyInterface
     */
    private function getNamingStrategy(): NamingStrategyInterface
    {
        return $this->namingStrategy ?? new DefaultNamingStrategy();
    }

    /**
     * @return MergeHandlerInterface
     */
    private function getMergeHandler(): MergeHandlerInterface
    {
        return $this->mergeHandler ?? new DefaultMergeHandler();
    }

    /**
     * @return ComparatorInterface
     */
    private function getComparator(): ComparatorInterface
    {
        return $this->comparator ?? new DefaultComparator();
    }

    /**
     * @return void
     * @throws ResolverException
     */
    private function assertNotFrozen(): void
    {
        if ($this->isFrozen) {
            throw new ResolverException(
                'Can not modify the factory while it\'s frozen. The factory gets frozen when the factory has already ' .
                'build a resolver builder factory'
            );
        }
    }
}
