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

    /**
     * @param HandlerFactory $handlerFactory
     * @return void
     */
    public function setHandlerFactory(HandlerFactory $handlerFactory): void
    {
        $this->handlerFactory = $handlerFactory;
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
        if (null === $this->handlerFactory) {
            $this->handlerFactory = new HandlerFactory();
        }

        return $this->handlerFactory;
    }
}