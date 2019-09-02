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

/**
 * @package Jojo1981\DataResolver\Handler\MergeHandler
 */
class DefaultMergeHandler implements MergeHandlerInterface
{
    /**
     * @param Context $context
     * @param array $elements
     * @return \stdClass|array
     */
    public function merge(Context $context, array $elements)
    {
        if (\is_array($context->getData())) {
            return $elements;
        }

        $result = new \stdClass();
        foreach ($elements as $propertyName => $value) {
            $result->$propertyName = $value;
        }

        return $result;
    }
}