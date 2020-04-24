<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Handler\MergeHandler;

use Jojo1981\DataResolver\Handler\MergeHandlerInterface;
use Jojo1981\DataResolver\Resolver\Context;
use stdClass;
use function array_filter;
use function array_push;
use function array_reduce;
use function is_array;

/**
 * @package Jojo1981\DataResolver\Handler\MergeHandler
 */
class DefaultMergeHandler implements MergeHandlerInterface
{
    /**
     * @param Context $context
     * @param array $elements
     * @return mixed
     */
    public function merge(Context $context, array $elements)
    {
        if ($this->areAllElementsIndexedArrays($elements)) {
            return $this->flattenElements($elements);
        }

        if (is_array($context->getData())) {
            return $elements;
        }

        $result = new stdClass();
        foreach ($elements as $propertyName => $value) {
            $result->$propertyName = $value;
        }

        return $result;
    }

    /**
     * @param array $elements
     * @return array
     */
    private function flattenElements(array $elements): array
    {
        return array_reduce(
            $elements,
            static function (array $result, array $element): array {
                if (!empty($element)) {
                    array_push($result, ...array_filter($element));
                }

                return $result;
            },
            []
        );
    }

    /**
     * @param array $elements
     * @return bool
     */
    private function areAllElementsIndexedArrays(array $elements): bool
    {
        foreach ($elements as $element) {
            if (!$this->isIndexedArray($element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param mixed $array
     * @return bool
     */
    private function isIndexedArray($array): bool
    {
        if (!is_array($array)) {
            return false;
        }

        foreach (\array_keys($array) as $key) {
            if (!\is_numeric($key)) {
                return false;
            }
        }

        return true;
    }
}
