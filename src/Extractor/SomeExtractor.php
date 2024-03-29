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
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Predicate\PredicateInterface;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
final class SomeExtractor extends AbstractSequenceExtractor
{
    /** @var SequenceHandlerInterface */
    private SequenceHandlerInterface $sequenceHandler;

    /** @var PredicateInterface */
    private PredicateInterface $predicate;

    /**
     * @param SequenceHandlerInterface $sequenceHandler
     * @param PredicateInterface $predicate
     */
    public function __construct(SequenceHandlerInterface $sequenceHandler, PredicateInterface $predicate)
    {
        $this->sequenceHandler = $sequenceHandler;
        $this->predicate = $predicate;
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
     * @return bool
     * @throws ExtractorException
     * @throws PredicateException
     * @throws HandlerException
     */
    protected function performExtract(Context $context): bool
    {
        foreach ($this->sequenceHandler->getIterator($context->getData()) as $key => $value) {
            $match = $this->predicate->match($context->copy()->pushPathPart($key)->setData($value));
            if (true === $match) {
                return true;
            }
        }

        return false;
    }
}
