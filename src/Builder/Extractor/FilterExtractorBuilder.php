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
use Jojo1981\DataResolver\Extractor\FilterExtractor;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Extractor
 */
class FilterExtractorBuilder implements ExtractorBuilderInterface
{
    /** @var SequenceHandlerInterface */
    private SequenceHandlerInterface $sequenceHandler;

    /** @var PredicateInterface */
    private PredicateInterface $predicate;

    /**
     * @param SequenceHandlerInterface $sequenceHandler
     * @param PredicateInterface $predicate
     */
    public function __construct(SequenceHandlerInterface $sequenceHandler, PredicateInterface $predicate)
    {
        $this->sequenceHandler = $sequenceHandler;
        $this->predicate = $predicate;
    }

    /**
     * @return FilterExtractor
     */
    public function build(): ExtractorInterface
    {
        return new FilterExtractor($this->sequenceHandler, $this->predicate);
    }
}
