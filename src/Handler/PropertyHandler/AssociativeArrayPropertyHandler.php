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
class AssociativeArrayPropertyHandler implements PropertyHandlerInterface
{
    /**
     * @param mixed $data
     * @return bool
     */
    public function supports($data): bool
    {
        return $this->isAssociativeArray($data);
    }

    /**
     * @param string $propertyName
     * @param mixed $data
     * @throws HandlerException
     * @return mixed
     */
    public function getValueForPropertyName(string $propertyName, $data)
    {
        if (!$this->supports($data)) {
            $this->throwUnsupportedException('getValueForPropertyName');
        }

        return $data[$propertyName];
    }

    /**
     * @param string $propertyName
     * @param mixed $data
     * @throws HandlerException
     * @return bool
     */
    public function hasValueForPropertyName(string $propertyName, $data): bool
    {
        if (!$this->supports($data)) {
            $this->throwUnsupportedException('hasValueForPropertyName');
        }

        return \array_key_exists($propertyName, $data);
    }

    /**
     * @param mixed $data
     * @return bool
     */
    private function isAssociativeArray($data): bool
    {
        if (!\is_array($data)) {
            return false;
        }

        foreach (\array_keys($data) as $key) {
            if (!\is_string($key)) {
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
            'The `%s` can only handle associative arrays. Illegal invocation of method `%s`. You should invoke the `%s` method first!',
            __CLASS__,
            $methodName,
            'supports'
        ));
    }
}