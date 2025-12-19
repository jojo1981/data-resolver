<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
declare(strict_types=1);

namespace Jojo1981\DataResolver\Builder\Extractor;

use Jojo1981\DataResolver\Builder\ExtractorBuilderInterface;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Extractor\SumExtractor;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Extractor
 */
final class SumExtractorBuilder implements ExtractorBuilderInterface
{
    /** @var SequenceHandlerInterface */
    private SequenceHandlerInterface $sequenceHandler;

    /**
     * @param SequenceHandlerInterface $sequenceHandler
     */
    public function __construct(SequenceHandlerInterface $sequenceHandler)
    {
        $this->sequenceHandler = $sequenceHandler;
    }

    /**
     * @return SumExtractor
     */
    public function build(): ExtractorInterface
    {
        return new SumExtractor($this->sequenceHandler);
    }
}
