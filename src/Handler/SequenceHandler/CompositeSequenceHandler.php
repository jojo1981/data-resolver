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

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Traversable;
use function array_walk;

/**
 * @package Jojo1981\DataResolver\Handler\SequenceHandler
 */
final class CompositeSequenceHandler implements SequenceHandlerInterface
{
    /** @var SequenceHandlerInterface[] */
    private array $handlers = [];

    /**
     * @param SequenceHandlerInterface[] $handlers
     */
    public function __construct(array $handlers)
    {
        array_walk($handlers, [$this, 'addHandler']);
    }

    /**
     * @param SequenceHandlerInterface $handler
     * @return void
     */
    private function addHandler(SequenceHandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    public function supports($data): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($data)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $data
     * @return Traversable
     * @throws HandlerException
     */
    public function getIterator($data): Traversable
    {
        return $this->getSupportedHandler('getIterator', $data)->getIterator($data);
    }

    /**
     * @param mixed $data
     * @param callable $callback
     * @return mixed
     * @throws HandlerException
     */
    public function filter($data, callable $callback)
    {
        return $this->getSupportedHandler('filter', $data)->filter($data, $callback);
    }

    /**
     * @param mixed $data
     * @return int
     * @throws HandlerException
     */
    public function count($data): int
    {
        return $this->getSupportedHandler('count', $data)->count($data);
    }

    /**
     * @param mixed $data
     * @param callable $callback
     * @return mixed
     * @throws HandlerException
     */
    public function flatten($data, callable $callback)
    {
        return $this->getSupportedHandler('flatten', $data)->flatten($data, $callback);
    }

    /**
     * @param string $methodName
     * @param $data
     * @return SequenceHandlerInterface
     * @throws HandlerException
     */
    private function getSupportedHandler(string $methodName, $data): SequenceHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($data)) {
                return $handler;
            }
        }

        throw HandlerException::IllegalMethodInvocation(__CLASS__, $methodName, 'supports', 'has no supported handler');
    }
}
