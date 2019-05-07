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

use Jojo1981\DataResolver\Factory\ExtractorBuilderFactory;
use Jojo1981\DataResolver\Factory\HandlerFactory;
use Jojo1981\DataResolver\Factory\PredicateBuilderFactory;
use Jojo1981\DataResolver\Factory\ResolverBuilderFactory;
use Jojo1981\DataResolver\Handler\PropertyHandler\AssociativeArrayPropertyHandler;
use Jojo1981\DataResolver\Handler\PropertyHandler\ObjectPropertyHandler;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\Handler\SequenceHandler\ArraySequenceHandler;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\DefaultNamingStrategy;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;

/**
 * @api
 * @package Jojo1981\DataResolver
 */
class Factory
{
    /** @var null|HandlerFactory */
    private $handlerFactory;

    /** @var ExtractorBuilderFactory */
    private $extractorBuilderFactory;

    /** @var PredicateBuilderFactory */
    private $predicateBuilderFactory;

    /** @var NamingStrategyInterface */
    private $namingStrategy;

    /** @var bool  */
    private $useDefaultPropertyHandlers = false;

    /** @var bool */
    private $useDefaultSequenceHandlers = false;

    /** @var PropertyHandlerInterface[] */
    private $propertyHandlers = [];

    /** @var SequenceHandlerInterface[] */
    private $sequenceHandlers = [];

    /**
     * @deprecated
     *
     * @param HandlerFactory $handlerFactory
     * @return void
     */
    public function setHandlerFactory(HandlerFactory $handlerFactory): void
    {
        $this->handlerFactory = $handlerFactory;
    }

    /**
     * @return $this
     */
    public function useDefaultPropertyHandlers(): self
    {
        $this->useDefaultPropertyHandlers = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function useDefaultSequenceHandlers(): self
    {
        $this->useDefaultSequenceHandlers = true;

        return $this;
    }

    /**
     * @param PropertyHandlerInterface $propertyHandler
     * @return $this
     */
    public function registerPropertyHandler(PropertyHandlerInterface $propertyHandler): self
    {
        if (!\in_array($propertyHandler, $this->propertyHandlers, true)) {
            $this->propertyHandlers[] = $propertyHandler;
        }

        return $this;
    }

    /**
     * @param SequenceHandlerInterface $sequenceHandler
     * @return $this
     */
    public function registerSequenceHandler(SequenceHandlerInterface $sequenceHandler): self
    {
        if (!\in_array($sequenceHandler, $this->sequenceHandlers, true)) {
            $this->sequenceHandlers[] = $sequenceHandler;
        }

        return $this;
    }

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @return $this
     */
    public function setNamingStrategy(NamingStrategyInterface $namingStrategy): self
    {
        $this->namingStrategy = $namingStrategy;

        return $this;
    }

    /**
     * @return ResolverBuilderFactory
     */
    public function getResolverBuilderFactory(): ResolverBuilderFactory
    {
        return new ResolverBuilderFactory(
            $this->getExtractorBuilderFactory(),
            $this->getPredicateBuilderFactory()
        );
    }

    /**
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
     * @return HandlerFactory
     */
    private function getHandlerFactory(): HandlerFactory
    {
        if (null !== $this->handlerFactory) {

            if (empty($this->propertyHandlers) || $this->useDefaultPropertyHandlers) {
                \array_push($this->propertyHandlers, ...$this->getDefaultPropertyHandlers());
            }

            if (empty($this->sequenceHandlers || $this->useDefaultSequenceHandlers)) {
                \array_push($this->sequenceHandlers, ...$this->getDefaultSequenceHandlers());
            }

            $this->handlerFactory = new HandlerFactory();
            $this->handlerFactory->setNamingStrategy($this->getNamingStrategy());
            $this->handlerFactory->setPropertyHandlers($this->propertyHandlers);
            $this->handlerFactory->setSequenceHandlers($this->sequenceHandlers);
        }

        return $this->handlerFactory;
    }

    /**
     * @return PropertyHandlerInterface[]
     */
    private function getDefaultPropertyHandlers(): array
    {
        return [
            new ObjectPropertyHandler($this->getNamingStrategy()),
            new AssociativeArrayPropertyHandler($this->getNamingStrategy())
        ];
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
}