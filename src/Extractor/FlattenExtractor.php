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

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
final class FlattenExtractor extends AbstractSequenceExtractor
{
    /** @var SequenceHandlerInterface */
    private SequenceHandlerInterface $sequenceHandler;

    /** @var ExtractorInterface */
    private ExtractorInterface $extractor;

    /**
     * @param SequenceHandlerInterface $sequenceHandler
     * @param ExtractorInterface $extractor
     */
    public function __construct(SequenceHandlerInterface $sequenceHandler, ExtractorInterface $extractor)
    {
        $this->sequenceHandler = $sequenceHandler;
        $this->extractor = $extractor;
    }

    /**
     * @return SequenceHandlerInterface
     */
    protected function getSequenceHandler(): SequenceHandlerInterface
    {
        return $this->sequenceHandler;
    }

    /**
     * @param Context $context
     * @return mixed
     * @throws HandlerException
     */
    protected function performExtract(Context $context)
    {
        return $this->sequenceHandler->flatten(
            $context->getData(),
            function ($value, $key) use ($context) {
                return $this->extractor->extract($context->copy()->pushPathPart($key)->setData($value));
            }
        );
    }
}
