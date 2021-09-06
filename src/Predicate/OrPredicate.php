<?php declare(strict_types=1);
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
final class OrPredicate implements PredicateInterface
{
    /** @var PredicateInterface */
    private PredicateInterface $leftPredicate;

    /** @var PredicateInterface */
    private PredicateInterface $rightPredicate;

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
     * @return bool
     * @throws ExtractorException
     * @throws HandlerException
     * @throws PredicateException
     */
    public function match(Context $context): bool
    {
        return $this->leftPredicate->match($context->copy()) || $this->rightPredicate->match($context->copy());
    }
}
