<?php declare(strict_types=1);
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
use Jojo1981\DataResolver\Predicate\PredicateInterface;
use Jojo1981\DataResolver\Predicate\StringContainsPredicate;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
final class StringContainsPredicateBuilder implements PredicateBuilderInterface
{
    /** @var string */
    private string $subString;

    /** @var bool */
    private bool $caseSensitive;

    /**
     * @param string $subString
     * @param bool $caseSensitive
     */
    public function __construct(string $subString, bool $caseSensitive)
    {
        $this->subString = $subString;
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * @return StringContainsPredicate
     */
    public function build(): PredicateInterface
    {
        return new StringContainsPredicate($this->subString, $this->caseSensitive);
    }
}
