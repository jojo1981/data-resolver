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
class ObjectPropertyHandler implements PropertyHandlerInterface
{
    /** @var \ReflectionClass[] */
    private $reflectionClasses = [];

    /**
     * @param string $propertyName
     * @param mixed $data
     * @return bool
     */
    public function supports(string $propertyName, $data): bool
    {
        return \is_object($data);
    }

    /**
     * @param string $propertyName
     * @param mixed $data
     * @throws HandlerException
     * @return mixed
     */
    public function getValueForPropertyName(string $propertyName, $data)
    {
        if (!$this->supports($propertyName, $data)) {
            $this->throwUnsupportedException('getValueForPropertyName');
        }

        $reflectionClass = $this->getReflectionClass($data);
        $methodName = 'get' . \ucfirst($propertyName);
        if (null !== $method = $this->getPublicMethod($methodName, $reflectionClass)) {
            return $method->invoke($data);
        }

        if (null !== $property = $this->getPublicProperty($propertyName, $reflectionClass)) {
            return $property->getValue($data);
        }

        return null;
    }

    /**
     * @param string $propertyName
     * @param mixed $data
     * @throws HandlerException
     * @return bool
     */
    public function hasValueForPropertyName(string $propertyName, $data): bool
    {
        if (!$this->supports($propertyName, $data)) {
            $this->throwUnsupportedException('hasValueForPropertyName');
        }

        $reflectionClass = $this->getReflectionClass($data);
        $methodName = 'get' . \ucfirst($propertyName);

        return null !== $this->getPublicMethod($methodName, $reflectionClass) ||
            null !== $this->getPublicProperty($propertyName, $reflectionClass);
    }

    /**
     * @param string $methodName
     * @param \ReflectionClass $reflectionClass
     * @return null|\ReflectionMethod
     */
    private function getPublicMethod(string $methodName, \ReflectionClass $reflectionClass): ?\ReflectionMethod
    {
        try {
            $method = $reflectionClass->getMethod($methodName);
            if ($method->isPublic()) {
                return $method;
            }
        } catch (\ReflectionException $exception) {
            // nothing to do, just catch
        }

        return null;
    }

    /**
     * @param string $propertyName
     * @param \ReflectionClass $reflectionClass
     * @return null|\ReflectionProperty
     */
    private function getPublicProperty(string $propertyName, \ReflectionClass $reflectionClass): ?\ReflectionProperty
    {
        try {
            $property = $reflectionClass->getProperty($propertyName);
            if ($property->isPublic()) {
                return $property;
            }
        } catch (\ReflectionException $exception) {
            // nothing to do, just catch
        }

        return null;
    }

    /**
     *
     * @param object $data
     * @return \ReflectionClass
     * @throws HandlerException
     */
    private function getReflectionClass($data): \ReflectionClass
    {
        $className = \get_class($data);
        if (!\array_key_exists($className, $this->reflectionClasses)) {
            try {
                $this->reflectionClasses[$className] = new \ReflectionClass($data);
            } catch (\ReflectionException $exception) {
                throw new HandlerException('Can not get reflection', 0, $exception);
            }
        }

        return $this->reflectionClasses[$className];
    }

    /**
     * @param string $methodName
     * @throws HandlerException
     * @return void
     */
    private function throwUnsupportedException(string $methodName): void
    {
        throw new HandlerException(\sprintf(
            'The `%s` can only handle objects. Illegal invocation of method `%s`. You should invoke the `%s` method first!',
            __CLASS__,
            $methodName,
            'supports'
        ));
    }
}