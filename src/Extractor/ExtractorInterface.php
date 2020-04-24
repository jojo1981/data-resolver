<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Extractor;

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * An extractor is a class which can extract data from the given data.
 * For example get a property from an object or an array value by it's key.
 * Filtering arrays and find item in an array
 *
 * @package Jojo1981\DataResolver\Extractor
 */
interface ExtractorInterface
{
    /**
     * @param Context $context
     * @return mixed
     * @throws ExtractorException
     * @throws PredicateException
     * @throws HandlerException
     */
    public function extract(Context $context);
}
