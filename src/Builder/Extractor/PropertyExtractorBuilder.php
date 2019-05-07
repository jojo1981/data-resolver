<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Builder\Extractor;

use Jojo1981\DataResolver\Builder\ExtractorBuilderInterface;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Extractor\PropertyExtractor;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;

/**
 * @internal
 * @package Jojo1981\DataResolver\Builder\Extractor
 */
class PropertyExtractorBuilder implements ExtractorBuilderInterface
{
    /** @var NamingStrategyInterface */
    private $namingStrategy;

    /** @var PropertyHandlerInterface */
    private $propertyHandler;

    /** @var string */
    private $propertyName;

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @param PropertyHandlerInterface $propertyHandler
     * @param string $propertyName
     */
    public function __construct(
        NamingStrategyInterface $namingStrategy,
        PropertyHandlerInterface $propertyHandler,
        string $propertyName
    ) {
        $this->namingStrategy = $namingStrategy;
        $this->propertyHandler = $propertyHandler;
        $this->propertyName = $propertyName;
    }

    /**
     * @return PropertyExtractor
     */
    public function build(): ExtractorInterface
    {
        return new PropertyExtractor($this->namingStrategy, $this->propertyHandler, $this->propertyName);
    }
}