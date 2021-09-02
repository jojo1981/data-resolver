<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Handler\SequenceHandler;

use ArrayIterator;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Traversable;
use function array_filter;
use function array_keys;
use function array_push;
use function array_values;
use function count;
use function is_array;
use function is_numeric;

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
        return $this->isIndexedArray($data);
    }

    /**
     * @param mixed $data
     * @return Traversable
     * @throws HandlerException
     */
    public function getIterator($data): Traversable
    {
        if (!$this->supports($data)) {
            $this->throwUnsupportedException('getIterator');
        }

        return new ArrayIterator($data);
    }

    /**
     * @param mixed $data
     * @param callable $callback
     * @return mixed
     * @throws HandlerException
     */
    public function filter($data, callable $callback)
    {
        if (!$this->supports($data)) {
            $this->throwUnsupportedException('filter');
        }

        return array_filter($data, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @param mixed $data
     * @return int
     * @throws HandlerException
     */
    public function count($data): int
    {
        if (!$this->supports($data)) {
            $this->throwUnsupportedException('count');
        }

        return count($data);
    }

    /**
     * @param mixed $data
     * @param callable $callback
     * @return array
     * @throws HandlerException
     */
    public function flatten($data, callable $callback): array
    {
        if (!$this->supports($data)) {
            $this->throwUnsupportedException('flatten');
        }

        $result = [];
        foreach ($data as $key => $value) {
            $items = $callback($value, $key);
            if (null === $items) {
                continue;
            }

            $items = !is_array($items) ? [$items] : array_values($items);
            if (!empty($items)) {
                array_push($result, ...$items);
            }
        }

        return $result;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    private function isIndexedArray($data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        foreach (array_keys($data) as $key) {
            if (!is_numeric($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $methodName
     * @return void
     * @throws HandlerException
     */
    private function throwUnsupportedException(string $methodName): void
    {
        throw HandlerException::IllegalMethodInvocation(
            __CLASS__,
            $methodName,
            'supports',
            'can only handle indexed arrays'
        );
    }
}
