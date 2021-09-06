<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Builder\Predicate;

use Jojo1981\DataResolver\Builder\PredicateBuilderInterface;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\IsEmptyPredicate;
use Jojo1981\DataResolver\Predicate\PredicateInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
final class IsEmptyPredicateBuilder implements PredicateBuilderInterface
{
    /** @var SequenceHandlerInterface */
    private SequenceHandlerInterface $sequenceHandler;

    /**
     * @param SequenceHandlerInterface $sequenceHandler
     */
    public function __construct(SequenceHandlerInterface $sequenceHandler)
    {
        $this->sequenceHandler = $sequenceHandler;
    }

    /**
     * @return IsEmptyPredicate
     */
    public function build(): PredicateInterface
    {
        return new IsEmptyPredicate($this->sequenceHandler);
    }
}
