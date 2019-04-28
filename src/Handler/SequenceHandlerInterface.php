<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Handler;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;

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
    public function supports($data): bool;

    /**
     * @param mixed $data
     * @throws HandlerException
     * @return \Traversable
     */
    public function getIterator($data): \Traversable;

    /**
     * @param mixed $data
     * @param callable $callback
     * @throws HandlerException
     * @return mixed
     */
    public function filter($data, callable $callback);

    /**
     * @param mixed $data
     * @param callable $callback
     * @throws HandlerException
     * @return mixed
     */
    public function flatten($data, callable $callback);
}