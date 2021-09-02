<?php declare(strict_types=1);
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
use Jojo1981\DataResolver\Handler\MergeHandlerInterface;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;

/**
 * @internal
 * @package Jojo1981\DataResolver\Builder\Extractor
 */
class PropertyExtractorBuilder implements ExtractorBuilderInterface
{
    /** @var NamingStrategyInterface */
    private NamingStrategyInterface $namingStrategy;

    /** @var PropertyHandlerInterface */
    private PropertyHandlerInterface $propertyHandler;

    /** @var MergeHandlerInterface */
    private MergeHandlerInterface $mergeHandler;

    /** @var string[] */
    private array $propertyNames;

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @param PropertyHandlerInterface $propertyHandler
     * @param MergeHandlerInterface $mergeHandler
     * @param string[] $propertyNames
     */
    public function __construct(
        NamingStrategyInterface $namingStrategy,
        PropertyHandlerInterface $propertyHandler,
        MergeHandlerInterface $mergeHandler,
        array $propertyNames
    ) {
        $this->namingStrategy = $namingStrategy;
        $this->propertyHandler = $propertyHandler;
        $this->mergeHandler = $mergeHandler;
        $this->propertyNames = $propertyNames;
    }

    /**
     * @return PropertyExtractor
     */
    public function build(): ExtractorInterface
    {
        return new PropertyExtractor(
            $this->namingStrategy,
            $this->propertyHandler,
            $this->mergeHandler,
            $this->propertyNames
        );
    }
}
