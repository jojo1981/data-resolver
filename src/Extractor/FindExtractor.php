<?php
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
class FindExtractor extends AbstractSequenceExtractor
{
    /** @var PredicateInterface */
    private $predicate;

    /** @var SequenceHandlerInterface */
    private $sequenceHandler;

    /**
     * @param SequenceHandlerInterface $sequenceHandler
     * @param PredicateInterface $predicate
     */
    public function __construct(SequenceHandlerInterface $sequenceHandler, PredicateInterface $predicate)
    {
        $this->predicate = $predicate;
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
     * @throws HandlerException
     * @throws ExtractorException
     * @throws PredicateException
     * @return mixed
     */
    protected function performExtract(Context $context)
    {
        $data = $context->getData();

        foreach ($this->sequenceHandler->getIterator($data) as $key => $value) {
            $match = $this->predicate->match($context->copy()->pushPathPart($key)->setData($value));
            if ($match) {
                return $value;
            }
        }

        return null;
    }
}