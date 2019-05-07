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
class ObjectPropertyHandler implements PropertyHandlerInterface
{
    /** @var NamingStrategyInterface */
    private $namingStrategy;

    /** @var \ReflectionClass[] */
    private $reflectionClasses = [];

    /**
     * @param NamingStrategyInterface $namingStrategy
     */
    public function __construct(NamingStrategyInterface $namingStrategy)
    {
        $this->namingStrategy = $namingStrategy;
    }

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

        if ($data instanceof \stdClass) {
            $objectVars = \get_object_vars($data);
            foreach ($this->namingStrategy->getPropertyNames($propertyName) as $propName) {
                if (\array_key_exists($propName, $objectVars)) {
                    return $objectVars[$propName];
                }
            }
        } else {
            $reflectionClass = $this->getReflectionClass($data);

            foreach ($this->namingStrategy->getMethodNames($propertyName) as $methodName) {
                if (null !== $method = $this->getPublicMethod($methodName, $reflectionClass)) {
                    return $method->invoke($data);
                }
            }

            foreach ($this->namingStrategy->getPropertyNames($propertyName) as $propName) {
                if (null !== $property = $this->getPublicProperty($propName, $reflectionClass)) {
                    return $property->getValue($data);
                }
            }
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

        if ($data instanceof \stdClass) {
            $objectVars = \get_object_vars($data);
            foreach ($this->namingStrategy->getPropertyNames($propertyName) as $propName) {
                if (\array_key_exists($propName, $objectVars)) {
                    return true;
                }
            }
        } else {
            $reflectionClass = $this->getReflectionClass($data);
            foreach ($this->namingStrategy->getMethodNames($propertyName) as $methodName) {
                if (null !== $this->getPublicMethod($methodName, $reflectionClass)) {
                    return true;
                }
            }

            foreach ($this->namingStrategy->getPropertyNames($propertyName) as $propName) {
                if (null !== $this->getPublicProperty($propName, $reflectionClass)) {
                    return true;
                }
            }
        }

        return false;
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
     * @throws HandlerException
     * @return \ReflectionClass
     */
    private function getReflectionClass($data): \ReflectionClass
    {
        $className = \get_class($data);
        if (!\array_key_exists($className, $this->reflectionClasses)) {
            try {
                $this->reflectionClasses[$className] = new \ReflectionClass(
                    \defined('FAKE_REFLECTION_EXCEPTION') ? 'A\Non\Existing\Class' : $data
                );
            } catch (\ReflectionException $exception) {
                throw HandlerException::couldNotGetReflection($exception);
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
        throw HandlerException::IllegalMethodInvocation(__CLASS__, $methodName, 'supports', 'can only handle objects');
    }
}