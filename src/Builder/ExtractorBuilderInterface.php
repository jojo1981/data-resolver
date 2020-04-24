<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Builder;

use Jojo1981\DataResolver\Extractor\ExtractorInterface;

/**
 * @package Jojo1981\DataResolver\Builder
 */
interface ExtractorBuilderInterface
{
    /**
     * @return ExtractorInterface
     */
    public function build(): ExtractorInterface;
}
