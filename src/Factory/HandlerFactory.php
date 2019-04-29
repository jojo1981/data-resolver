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

use Jojo1981\DataResolver\Handler\PropertyHandler\AssociativeArrayPropertyHandler;
use Jojo1981\DataResolver\Handler\PropertyHandler\CompositePropertyHandler;
use Jojo1981\DataResolver\Handler\PropertyHandler\ObjectPropertyHandler;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\Handler\SequenceHandler\ArraySequenceHandler;
use Jojo1981\DataResolver\Handler\SequenceHandler\CompositeSequenceHandler;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\DefaultNamingStrategy;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;

/**
 * @package Jojo1981\DataResolver\Factory
 */
class HandlerFactory
{
    /** @var PropertyHandlerInterface[] */
    private $propertyHandlers = [];

    /** @var SequenceHandlerInterface[] */
    private $sequenceHandlers = [];

    /** @var NamingStrategyInterface */
    private $namingStrategy;

    /**
     * @param PropertyHandlerInterface[] $propertyHandlers
     * @return void
     */
    public function setPropertyHandlers(array $propertyHandlers): void
    {
        $this->propertyHandlers = [];
        \array_walk($propertyHandlers, [$this, 'addPropertyHandler']);
    }

    /**
     * @param SequenceHandlerInterface[] $sequenceHandlers
     * @return void
     */
    public function setSequenceHandlers(array $sequenceHandlers): void
    {
        $this->sequenceHandlers = [];
        \array_walk($sequenceHandlers, [$this, 'addSequenceHandler']);
    }

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @return void
     */
    public function setNamingStrategy(NamingStrategyInterface $namingStrategy): void
    {
        $this->namingStrategy = $namingStrategy;
    }

    /**
     * @return PropertyHandlerInterface
     */
    public function getPropertyHandler(): PropertyHandlerInterface
    {
        return new CompositePropertyHandler($this->getPropertyHandlers());
    }

    /**
     * @return SequenceHandlerInterface
     */
    public function getSequenceHandler(): SequenceHandlerInterface
    {
        return new CompositeSequenceHandler($this->getSequenceHandlers());
    }

    /**
     * @return PropertyHandlerInterface[]
     */
    private function getPropertyHandlers(): array
    {
        if (empty($this->propertyHandlers)) {
            $this->propertyHandlers = $this->getDefaultPropertyHandlers();
        }

        return $this->propertyHandlers;
    }

    /**
     * @return SequenceHandlerInterface[]
     */
    private function getSequenceHandlers(): array
    {
        if (empty($this->sequenceHandlers)) {
            $this->sequenceHandlers = $this->getDefaultSequenceHandlers();
        }

        return $this->sequenceHandlers;
    }

    /**
     * @param PropertyHandlerInterface $propertyHandler
     * @return void
     */
    private function addPropertyHandler(PropertyHandlerInterface $propertyHandler): void
    {
        $this->propertyHandlers[] = $propertyHandler;
    }

    /**
     * @param SequenceHandlerInterface $sequenceHandler
     * @return void
     */
    private function addSequenceHandler(SequenceHandlerInterface $sequenceHandler): void
    {
        $this->sequenceHandlers[] = $sequenceHandler;
    }

    /**
     * @return PropertyHandlerInterface[]
     */
    private function getDefaultPropertyHandlers(): array
    {
        return [
            new ObjectPropertyHandler($this->geNamingStrategy()),
            new AssociativeArrayPropertyHandler($this->geNamingStrategy())
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
    private function geNamingStrategy(): NamingStrategyInterface
    {
        if (null === $this->namingStrategy) {
            $this->namingStrategy = new DefaultNamingStrategy();
        }

        return $this->namingStrategy;
    }
}