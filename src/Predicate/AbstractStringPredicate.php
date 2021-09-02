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

use Jojo1981\DataResolver\Resolver\Context;
use function is_string;

/**
 * @package Jojo1981\DataResolver\Predicate
 */
abstract class AbstractStringPredicate implements PredicateInterface
{
    /**
     * @param Context $context
     * @return bool
     */
    final public function match(Context $context): bool
    {
        $data = $context->getData();

        return is_string($data) && $this->performMatch($data);
    }

    /**
     * @param mixed $data
     * @return bool
     */
    abstract protected function performMatch($data): bool;
}
