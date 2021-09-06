<?php declare(strict_types=1);
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
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use stdClass;
use function array_key_exists;
use function defined;
use function get_class;
use function get_object_vars;
use function is_object;

/**
 * @package Jojo1981\DataResolver\Handler\PropertyHandler
 */
final class ObjectPropertyHandler implements PropertyHandlerInterface
{
    /** @var ReflectionClass[] */
    private array $reflectionClasses = [];

    /**
     * @param string $propertyName
     * @param mixed $data
     * @return bool
     */
    public function supports(string $propertyName, $data): bool
    {
        return is_object($data);
    }

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @param string $propertyName
     * @param mixed $data
     * @return mixed
     * @throws ReflectionException
     * @throws HandlerException
     */
    public function getValueForPropertyName(NamingStrategyInterface $namingStrategy, string $propertyName, $data)
    {
        if (!$this->supports($propertyName, $data)) {
            $this->throwUnsupportedException('getValueForPropertyName');
        }

        if ($data instanceof stdClass) {
            $objectVars = get_object_vars($data);
            foreach ($namingStrategy->getPropertyNames($propertyName) as $propName) {
                if (array_key_exists($propName, $objectVars)) {
                    return $objectVars[$propName];
                }
            }
        } else {
            $reflectionClass = $this->getReflectionClass($data);

            foreach ($namingStrategy->getMethodNames($propertyName) as $methodName) {
                if (null !== $method = $this->getPublicMethod($methodName, $reflectionClass)) {
                    return $method->invoke($data);
                }
            }

            foreach ($namingStrategy->getPropertyNames($propertyName) as $propName) {
                if (null !== $property = $this->getPublicProperty($propName, $reflectionClass)) {
                    return $property->getValue($data);
                }
            }
        }

        return null;
    }

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @param string $propertyName
     * @param mixed $data
     * @return bool
     * @throws HandlerException
     */
    public function hasValueForPropertyName(NamingStrategyInterface $namingStrategy, string $propertyName, $data): bool
    {
        if (!$this->supports($propertyName, $data)) {
            $this->throwUnsupportedException('hasValueForPropertyName');
        }

        if ($data instanceof stdClass) {
            $objectVars = get_object_vars($data);
            foreach ($namingStrategy->getPropertyNames($propertyName) as $propName) {
                if (array_key_exists($propName, $objectVars)) {
                    return true;
                }
            }
        } else {
            $reflectionClass = $this->getReflectionClass($data);
            foreach ($namingStrategy->getMethodNames($propertyName) as $methodName) {
                if (null !== $this->getPublicMethod($methodName, $reflectionClass)) {
                    return true;
                }
            }

            foreach ($namingStrategy->getPropertyNames($propertyName) as $propName) {
                if (null !== $this->getPublicProperty($propName, $reflectionClass)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $methodName
     * @param ReflectionClass $reflectionClass
     * @return ReflectionMethod|null
     */
    private function getPublicMethod(string $methodName, ReflectionClass $reflectionClass): ?ReflectionMethod
    {
        try {
            $method = $reflectionClass->getMethod($methodName);
            if ($method->isPublic()) {
                return $method;
            }
        } catch (ReflectionException $exception) {
            // nothing to do, just catch
        }

        return null;
    }

    /**
     * @param string $propertyName
     * @param ReflectionClass $reflectionClass
     * @return ReflectionProperty|null
     */
    private function getPublicProperty(string $propertyName, ReflectionClass $reflectionClass): ?ReflectionProperty
    {
        try {
            $property = $reflectionClass->getProperty($propertyName);
            if ($property->isPublic()) {
                return $property;
            }
        } catch (ReflectionException $exception) {
            // nothing to do, just catch
        }

        return null;
    }

    /**
     *
     * @param object $data
     * @return ReflectionClass
     * @throws HandlerException
     */
    private function getReflectionClass(object $data): ReflectionClass
    {
        $className = get_class($data);
        if (!array_key_exists($className, $this->reflectionClasses)) {
            try {
                $this->reflectionClasses[$className] = new ReflectionClass(
                    defined('FAKE_REFLECTION_EXCEPTION') ? 'A\Non\Existing\Class' : $data
                );
            } catch (ReflectionException $exception) {
                throw HandlerException::couldNotGetReflection($exception);
            }
        }

        return $this->reflectionClasses[$className];
    }

    /**
     * @param string $methodName
     * @return void
     * @throws HandlerException
     */
    private function throwUnsupportedException(string $methodName): void
    {
        throw HandlerException::IllegalMethodInvocation(__CLASS__, $methodName, 'supports', 'can only handle objects');
    }
}
