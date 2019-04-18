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
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\NonePredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
class NonePredicateBuilder implements PredicateBuilderInterface
{
    /** @var SequenceHandlerInterface */
    private $sequenceHandler;

    /** @var PredicateBuilderInterface */
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
     * @return PredicateInterface
     */
    public function build(): PredicateInterface
    {
        return new NonePredicate($this->sequenceHandler, $this->predicateBuilder->build());
    }
}