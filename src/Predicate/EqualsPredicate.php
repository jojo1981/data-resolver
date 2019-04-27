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

use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\Constraint\IsEqual as IsEqualConstraint;

/**
 * @package Jojo1981\DataResolver\Predicate
 */
class EqualsPredicate implements PredicateInterface
{
    /** @var IsEqualConstraint */
    private $isEqualConstraint;

    /**
     * @param mixed $expectedValue
     */
    public function __construct($expectedValue)
    {
        $this->isEqualConstraint = new IsEqualConstraint($expectedValue, 0.0, 25, true);
    }

    /**
     * @param Context $context
     * @return bool
     */
    public function match(Context $context): bool
    {
        try {
            return $this->isEqualConstraint->evaluate($context->getData(), '', true);
        } catch (\Exception $exception) {
            return false;
        }
    }
}