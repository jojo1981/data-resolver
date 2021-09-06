<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @package Jojo1981\DataResolver\Predicate
 */
final class IsEmptyPredicate implements PredicateInterface
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
     * @param Context $context
     * @return bool
     * @throws HandlerException
     */
    public function match(Context $context): bool
    {
        if ($this->sequenceHandler->supports($context->getData())) {
            return 0 === $this->sequenceHandler->count($context->getData());
        }

        return empty($context->getData());
    }
}
