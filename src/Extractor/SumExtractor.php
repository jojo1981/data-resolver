<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Extractor;

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Resolver\Context;
use function get_class;
use function is_numeric;
use function sprintf;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
final class SumExtractor extends AbstractSequenceExtractor
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
     * @return SequenceHandlerInterface
     */
    protected function getSequenceHandler(): SequenceHandlerInterface
    {
        return $this->sequenceHandler;
    }

    /**
     * @param Context $context
     * @return float
     * @throws HandlerException
     * @throws ExtractorException
     */
    protected function performExtract(Context $context): float
    {
        $sum = 0.0;
        foreach ($this->sequenceHandler->getIterator($context->getData()) as $item) {
            if (!is_numeric($item)) {
                throw new ExtractorException(sprintf(
                    'Found a NOT numeric item. Could not extract data with `%s` at path: `%s`',
                    get_class($this),
                    $context->getPath()
                ));
            }

            $sum += (float)$item;
        }

        return $sum;
    }
}
