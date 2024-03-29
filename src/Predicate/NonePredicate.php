<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Extractor\Exception\ExtractorException;
use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\Exception\PredicateException;
use Jojo1981\DataResolver\Resolver\Context;
use function get_class;
use function sprintf;

/**
 * @package Jojo1981\DataResolver\Predicate
 */
final class NonePredicate implements PredicateInterface
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
     * @param Context $context
     * @return bool
     * @throws HandlerException
     * @throws PredicateException
     * @throws ExtractorException
     */
    public function match(Context $context): bool
    {
        if (!$this->sequenceHandler->supports($context->getData())) {
            throw new PredicateException(sprintf(
                'Could not match data with `%s` at path: `%s`',
                get_class($this),
                $context->getPath()
            ));
        }

        foreach ($this->sequenceHandler->getIterator($context->getData()) as $key => $value) {
            if (true === $this->predicate->match($context->copy()->pushPathPart($key)->setData($value))) {
                return false;
            }
        }

        return true;
    }
}
