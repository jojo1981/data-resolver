<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Builder\Predicate;

use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Extractor\HasPropertyExtractor;
use Jojo1981\DataResolver\Predicate\HasPropertyPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
class HasPropertyPredicateBuilder implements PredicateBuilderInterface
{
    /** @var HasPropertyExtractor */
    private $extractor;

    /**
     * @param HasPropertyExtractor $extractor
     */
    public function __construct(HasPropertyExtractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * @return HasPropertyPredicate
     */
    public function build(): PredicateInterface
    {
        return new HasPropertyPredicate($this->extractor);
    }
}