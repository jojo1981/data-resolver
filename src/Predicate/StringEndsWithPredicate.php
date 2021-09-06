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

use function strlen;
use function strtolower;
use function substr;

/**
 * @package Jojo1981\DataResolver\Predicate
 */
final class StringEndsWithPredicate extends AbstractStringPredicate
{
    /** @var string */
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
     * @param mixed $data
     * @return bool
     */
    protected function performMatch($data): bool
    {
        return $this->transform(substr($data, 0 - strlen($this->suffix))) === $this->transform($this->suffix);
    }

    /**
     * @param string $text
     * @return string
     */
    private function transform(string $text): string
    {
        return $this->caseSensitive ? $text : strtolower($text);
    }
}
