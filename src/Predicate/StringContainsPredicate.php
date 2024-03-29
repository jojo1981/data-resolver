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
final class StringContainsPredicate extends AbstractStringPredicate
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
     * @param mixed $data
     * @return bool
     */
    protected function performMatch($data): bool
    {
        if ($this->caseSensitive) {
            return false !== mb_strpos($data, $this->subString);
        }

        return false !== mb_stripos($data, $this->subString);
    }
}
