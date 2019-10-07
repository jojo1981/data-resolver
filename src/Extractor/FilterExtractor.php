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

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\PredicateInterface;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
class FilterExtractor extends AbstractSequenceExtractor
{
    /** @var SequenceHandlerInterface */
    private $sequenceHandler;

    /** @var PredicateInterface */
    private $predicate;

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
     * @throws HandlerException
     * @return mixed
     */
    protected function performExtract(Context $context)
    {
        return $this->sequenceHandler->filter(
            $context->getData(),
            function ($value) use ($context): bool {
                try {
                    return $this->predicate->match($context->copy()->setData($value));
                } catch (\Exception $exception) {
                    return false;
                }
            }
        );
    }
}