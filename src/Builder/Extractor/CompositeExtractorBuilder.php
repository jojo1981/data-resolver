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
use Jojo1981\DataResolver\Extractor\CompositeExtractor;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Extractor
 */
class CompositeExtractorBuilder implements ExtractorBuilderInterface
{
    /** @var ExtractorInterface */
    private $extractor1;

    /** @var ExtractorInterface */
    private $extractor2;

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
     * @return CompositeExtractor
     */
    public function build(): ExtractorInterface
    {
        return new CompositeExtractor($this->extractor1, $this->extractor2);
    }
}
