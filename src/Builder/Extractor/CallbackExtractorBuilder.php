<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Builder\Extractor;

use Jojo1981\DataResolver\Builder\ExtractorBuilderInterface;
use Jojo1981\DataResolver\Extractor\CallbackExtractor;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Extractor
 */
class CallbackExtractorBuilder implements ExtractorBuilderInterface
{
    /** @var callable */
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return CallbackExtractor
     */
    public function build(): ExtractorInterface
    {
        return new CallbackExtractor($this->callback);
    }
}
