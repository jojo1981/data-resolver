<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Builder\Extractor;

use Jojo1981\DataResolver\Builder\ExtractorBuilderInterface;
use Jojo1981\DataResolver\Builder\ResolverBuilder;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Extractor\ResolverExtractor;

/**
 * @package Jojo1981\DataResolver\Builder\Extractor
 */
class ResolverExtractorBuilder implements ExtractorBuilderInterface
{
    /** @var ResolverBuilder */
    private $resolverBuilder;

    /**
     * @param ResolverBuilder $resolverBuilder
     */
    public function __construct(ResolverBuilder $resolverBuilder)
    {
        $this->resolverBuilder = $resolverBuilder;
    }

    /**
     * @return ExtractorInterface
     */
    public function build(): ExtractorInterface
    {
        return new ResolverExtractor($this->resolverBuilder->build());
    }
}