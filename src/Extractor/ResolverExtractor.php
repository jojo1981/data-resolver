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
use Jojo1981\DataResolver\Resolver;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
class ResolverExtractor implements ExtractorInterface
{
    /** @var Resolver */
    private $resolver;

    /**
     * @param Resolver $resolver
     */
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param Context $context
     * @return mixed
     * @throws PredicateException
     * @throws HandlerException
     * @throws ExtractorException
     */
    public function extract(Context $context)
    {
        return $this->resolver->resolve($context);
    }
}
