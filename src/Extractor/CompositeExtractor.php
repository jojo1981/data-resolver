<?php declare(strict_types=1);
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
 * @package Jojo1981\DataResolver\Extractor
 */
final class CompositeExtractor implements ExtractorInterface
{
    /** @var ExtractorInterface */
    private ExtractorInterface $extractor1;

    /** @var ExtractorInterface */
    private ExtractorInterface $extractor2;

    /**
     * @param ExtractorInterface $extractor1
     * @param ExtractorInterface $extractor2
     */
    public function __construct(ExtractorInterface $extractor1, ExtractorInterface $extractor2)
    {
        $this->extractor1 = $extractor1;
        $this->extractor2 = $extractor2;
    }

    /**
     * @param Context $context
     * @return mixed
     * @throws ExtractorException
     * @throws PredicateException
     * @throws HandlerException
     */
    public function extract(Context $context)
    {
        return $this->extractor2->extract($context->copy()->setData($this->extractor1->extract($context)));
    }
}
