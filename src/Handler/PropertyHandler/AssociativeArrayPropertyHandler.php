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
use function array_key_exists;
use function array_keys;
use function is_array;
use function is_string;
use function sprintf;

/**
 * @package Jojo1981\DataResolver\Handler\PropertyHandler
 */
final class AssociativeArrayPropertyHandler implements PropertyHandlerInterface
{
    /**
     * @param string $propertyName
     * @param mixed $data
     * @return bool
     */
    public function supports(string $propertyName, $data): bool
    {
        return $this->isAssociativeArray($data);
    }

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @param string $propertyName
     * @param mixed $data
     * @return mixed
     * @throws HandlerException
     */
    public function getValueForPropertyName(NamingStrategyInterface $namingStrategy, string $propertyName, $data)
    {
        if (!$this->supports($propertyName, $data)) {
            $this->throwUnsupportedException('getValueForPropertyName');
        }

        foreach ($namingStrategy->getPropertyNames($propertyName) as $propName) {
            if (array_key_exists($propName, $data)) {
                return $data[$propName];
            }
        }

        throw HandlerException::IllegalMethodInvocation(
            __CLASS__,
            'getValueForPropertyName',
            'hasValueForPropertyName',
            sprintf('can not find a value for property name `%s`', $propertyName)
        );
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

        foreach ($namingStrategy->getPropertyNames($propertyName) as $propName) {
            if (array_key_exists($propName, $data)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    private function isAssociativeArray($data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        foreach (array_keys($data) as $key) {
            if (!is_string($key)) {
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
            'can only handle associative arrays'
        );
    }
}
