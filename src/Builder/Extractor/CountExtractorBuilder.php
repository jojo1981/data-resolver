<?php declare(strict_types=1);
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
use Jojo1981\DataResolver\Extractor\CountExtractor;
use Jojo1981\DataResolver\Extractor\ExtractorInterface;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;

/**
 * @package Jojo1981\DataResolver\Builder\Extractor
 */
final class CountExtractorBuilder implements ExtractorBuilderInterface
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
     * @return CountExtractor
     */
    public function build(): ExtractorInterface
    {
        return new CountExtractor($this->sequenceHandler);
    }
}
