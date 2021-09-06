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
use Jojo1981\DataResolver\Predicate\StringEndsWithPredicate;

/**
 * @package Jojo1981\DataResolver\Builder\Predicate
 */
final class StringEndsWithPredicateBuilder implements PredicateBuilderInterface
{
    private string $suffix;

    /** @var bool */
    private bool $caseSensitive;

    /**
     * @param string $suffix
     * @param bool $caseSensitive
     */
    public function __construct(string $suffix, bool $caseSensitive)
    {
        $this->suffix = $suffix;
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * @return StringEndsWithPredicate
     */
    public function build(): PredicateInterface
    {
        return new StringEndsWithPredicate($this->suffix, $this->caseSensitive);
    }
}
