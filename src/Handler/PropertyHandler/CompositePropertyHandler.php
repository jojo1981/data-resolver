<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Handler\PropertyHandler;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;

/**
 * @package Jojo1981\DataResolver\Handler\PropertyHandler
 */
class CompositePropertyHandler implements PropertyHandlerInterface
{
    /** @var PropertyHandlerInterface[] */
    private $handlers = [];

    /**
     * @param PropertyHandlerInterface[] $handlers
     */
    public function __construct(array $handlers)
    {
        \array_walk($handlers, [$this, 'addHandler']);
    }

    /**
     * @param PropertyHandlerInterface $handler
     * @return void
     */
    private function addHandler(PropertyHandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param string $propertyName
     * @param mixed $data
     * @return bool
     */
    public function supports(string $propertyName, $data): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($propertyName, $data)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $propertyName
     * @param mixed $data
     * @throws HandlerException
     * @return mixed
     */
    public function getValueForPropertyName(string $propertyName, $data)
    {
        return $this->getSupportedHandler('getValueForPropertyName', $propertyName, $data)
            ->getValueForPropertyName($propertyName, $data);
    }

    /**
     * @param string $propertyName
     * @param mixed $data
     * @throws HandlerException
     * @return bool
     */
    public function hasValueForPropertyName(string $propertyName, $data): bool
    {
        return $this->getSupportedHandler('hasValueForPropertyName', $propertyName, $data)
            ->hasValueForPropertyName($propertyName, $data);
    }

    /**
     * @param string $methodName
     * @param string $propertyName
     * @param $data
     * @throws HandlerException
     * @return PropertyHandlerInterface
     */
    private function getSupportedHandler(string $methodName, string $propertyName, $data): PropertyHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($propertyName, $data)) {
                return $handler;
            }
        }

        throw new HandlerException(\sprintf(
            'The `%s` can has no supported handler. Illegal invocation of method `%s`. You should invoke the `%s` method first!',
            __CLASS__,
            $methodName,
            'supports'
        ));
    }
}