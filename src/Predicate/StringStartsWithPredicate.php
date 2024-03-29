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

use function mb_stripos;
use function mb_strpos;

/**
 * @package Jojo1981\DataResolver\Predicate
 */
final class StringStartsWithPredicate extends AbstractStringPredicate
{
    /** @var string */
    private string $prefix;

    /** @var bool */
    private bool $caseSensitive;

    /**
     * @param string $prefix
     * @param bool $caseSensitive
     */
    public function __construct(string $prefix, bool $caseSensitive)
    {
        $this->prefix = $prefix;
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    protected function performMatch($data): bool
    {
        return 0 === ($this->caseSensitive ? mb_strpos($data, $this->prefix) : mb_stripos($data, $this->prefix));
    }
}
