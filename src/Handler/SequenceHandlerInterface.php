<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
declare(strict_types=1);

namespace Jojo1981\DataResolver\Handler;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Traversable;

/**
 * Sequence handlers can handle sequences and provide an iterator, filter and flatten method for the data it supports
 *
 * @package Jojo1981\DataResolver\Handler
 */
interface SequenceHandlerInterface
{
    /**
     * @param mixed $data
     * @return bool
     */
    public function supports(mixed $data): bool;

    /**
     * @param mixed $data
     * @return Traversable
     * @throws HandlerException
     */
    public function getIterator(mixed $data): Traversable;

    /**
     * @param mixed $data
     * @param callable $callback
     * @return mixed
     * @throws HandlerException
     */
    public function filter(mixed $data, callable $callback): mixed;

    /**
     * @param mixed $data
     * @return int
     * @throws HandlerException
     */
    public function count(mixed $data): int;

    /**
     * @param mixed $data
     * @param callable $callback
     * @return mixed
     * @throws HandlerException
     */
    public function flatten(mixed $data, callable $callback): mixed;
}
