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
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Extractor\StringLengthExtractor;

/**
 * @package Jojo1981\DataResolver\Builder\Extractor
 */
class StringLengthExtractorBuilder implements ExtractorBuilderInterface
{
    /**
     * @return StringLengthExtractor
     */
    public function build(): ExtractorInterface
    {
        return new StringLengthExtractor();
    }
}
