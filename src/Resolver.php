<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver;

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @api
 * @package Jojo1981\DataResolver\Resolver
 */
class Resolver
{
    /** @var ExtractorInterface[] */
    private $extractors = [];

    /** @var Context */
    private $context;

    /**
     * @param ExtractorInterface[] $extractors
     */
    public function __construct(array $extractors)
    {
        \array_walk($extractors, [$this, 'addExtractor']);
    }

    /**
     * @param ExtractorInterface $extractor
     * @return void
     */
    private function addExtractor(ExtractorInterface $extractor): void
    {
        $this->extractors[] = $extractor;
    }

    /**
     * @param mixed $data
     * @throws ExtractorException
     * @throws PredicateException
     * @throws HandlerException
     * @return mixed
     */
    public function resolve($data)
    {
        $this->context = $data instanceof Context ? $data : new Context(null);
        foreach ($this->extractors as $extractor) {
            if (!$data instanceof Context) {
                $this->context->setData($data);
            }
            $data = $extractor->extract($this->context);
        }

        return $data instanceof Context ? $data->getData() : $data;
    }
}