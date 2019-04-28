<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Handler\SequenceHandler;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;

/**
 * @package Jojo1981\DataResolver\Handler\SequenceHandler
 */
class ArraySequenceHandler implements SequenceHandlerInterface
{
    /**
     * @param mixed $data
     * @return bool
     */
    public function supports($data): bool
    {
        return $this->isSequenceArray($data);
    }

    /**
     * @param mixed $data
     * @throws HandlerException
     * @return \Traversable
     */
    public function getIterator($data): \Traversable
    {
        if (!$this->supports($data)) {
            $this->throwUnsupportedException('getIterator');
        }

        return new \ArrayIterator($data);
    }

    /**
     * @param mixed $data
     * @param callable $callback
     * @throws HandlerException
     * @return mixed
     */
    public function filter($data, callable $callback)
    {
        if (!$this->supports($data)) {
            $this->throwUnsupportedException('filter');
        }

        return \array_values(\array_filter($data, $callback));
    }

    /**
     * @param mixed $data
     * @param callable $callback
     * @throws HandlerException
     * @return mixed
     */
    public function flatten($data, callable $callback)
    {
        if (!$this->supports($data)) {
            $this->throwUnsupportedException('flatten');
        }

        $result = [];
        foreach ($data as $key => $value) {
            $item = (array) $callback($key, $value);
            if (!empty($item)) {
                \array_push($result, ...$item);
            }
        }

        return $result;
    }


    /**
     * @param mixed $data
     * @return bool
     */
    private function isSequenceArray($data): bool
    {
        if (!\is_array($data)) {
            return false;
        }

        foreach (\array_keys($data) as $key) {
            if (!\is_numeric($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $methodName
     * @throws HandlerException
     * @return void
     */
    private function throwUnsupportedException(string $methodName): void
    {
        throw new HandlerException(\sprintf(
            'The `%s` can only handle indexed arrays. Illegal invocation of method `%s`. You should invoke the `%s` method first!',
            __CLASS__,
            $methodName,
            'supports'
        ));
    }
}