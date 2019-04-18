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
use Jojo1981\DataResolver\Builder\Predicate\ConditionalPredicateBuilder;
use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Extractor\FilterExtractor;
use Jojo1981\DataResolver\Extractor\FindExtractor;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Extractor
 */
class FilterExtractorBuilder implements ExtractorBuilderInterface
{
    /** @var SequenceHandlerInterface */
    private $sequenceHandler;

    /** @var ConditionalPredicateBuilder */
    private $predicateBuilder;

    /**
     * @param SequenceHandlerInterface $sequenceHandler
     * @param PredicateBuilderInterface $predicateBuilder
     */
    public function __construct(SequenceHandlerInterface $sequenceHandler, PredicateBuilderInterface $predicateBuilder)
    {
        $this->sequenceHandler = $sequenceHandler;
        $this->predicateBuilder = $predicateBuilder;
    }

    /**
     * @return FindExtractor
     */
    public function build(): ExtractorInterface
    {
        return new FilterExtractor($this->sequenceHandler, $this->predicateBuilder->build());
    }
}