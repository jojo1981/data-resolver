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

use function preg_match;

/**
 * @package Jojo1981\DataResolver\Predicate
 */
class StringRegexPredicate extends AbstractStringPredicate
{
    /** @var string */
    private $pattern;

    /**
     * @param string $pattern
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    protected function performMatch($data): bool
    {
        return preg_match($this->pattern, $data) > 0;
    }
}
