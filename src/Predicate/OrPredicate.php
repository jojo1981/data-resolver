<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @package Jojo1981\DataResolver\Predicate
 */
class OrPredicate implements PredicateInterface
{
    /** @var PredicateInterface */
    private $leftPredicate;

    /** @var PredicateInterface */
    private $rightPredicate;

    /**
     * @param PredicateInterface $leftPredicate
     * @param PredicateInterface $rightPredicate
     */
    public function __construct(PredicateInterface $leftPredicate, PredicateInterface $rightPredicate)
    {
        $this->leftPredicate = $leftPredicate;
        $this->rightPredicate = $rightPredicate;
    }

    /**
     * @param Context $context
     * @throws PredicateException
     * @throws ExtractorException
     * @throws HandlerException
     * @return bool
     */
    public function match(Context $context): bool
    {
        return $this->leftPredicate->match($context->copy()) || $this->rightPredicate->match($context->copy());
    }
}