<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Handler;

use Jojo1981\DataResolver\Resolver\Context;

/**
 * The merge handler is responsible for merging values resolved for multiple properties.
 * Values can be merged into a new object or associative array or merged into an index array or
 * whatever suites your needs.
 *
 * @package Jojo1981\DataResolver\Handler
 */
interface MergeHandlerInterface
{
    /**
     * @param Context $context
     * @param array $elements
     * @return mixed
     */
    public function merge(Context $context, array $elements);
}
