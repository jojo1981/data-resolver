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
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;

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
     * @param NamingStrategyInterface $namingStrategy
     * @param string $propertyName
     * @param mixed $data
     * @throws HandlerException
     * @return mixed
     */
    public function getValueForPropertyName(NamingStrategyInterface $namingStrategy, string $propertyName, $data)
    {
        return $this->getSupportedHandler('getValueForPropertyName', $propertyName, $data)
            ->getValueForPropertyName($namingStrategy, $propertyName, $data);
    }

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @param string $propertyName
     * @param mixed $data
     * @throws HandlerException
     * @return bool
     */
    public function hasValueForPropertyName(NamingStrategyInterface $namingStrategy, string $propertyName, $data): bool
    {
        return $this->getSupportedHandler('hasValueForPropertyName', $propertyName, $data)
            ->hasValueForPropertyName($namingStrategy,$propertyName, $data);
    }

    /**
     * @param string $methodName
     * @param string $propertyName
     * @param mixed $data
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

        throw HandlerException::IllegalMethodInvocation(__CLASS__, $methodName, 'supports', 'has no supported handler');
    }
}