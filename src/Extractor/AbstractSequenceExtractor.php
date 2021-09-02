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
use Jojo1981\DataResolver\Resolver\Context;
use function get_class;
use function sprintf;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
abstract class AbstractSequenceExtractor implements ExtractorInterface
{
    /**
     * @param Context $context
     * @return mixed
     * @throws PredicateException
     * @throws ExtractorException
     * @throws HandlerException
     */
    final public function extract(Context $context)
    {
        if (!$this->getSequenceHandler()->supports($context->getData())) {
            throw new ExtractorException(sprintf(
                'Could not extract data with `%s` at path: `%s`',
                get_class($this),
                $context->getPath()
            ));
        }

        return $this->performExtract($context);
    }

    /**
     * @return SequenceHandlerInterface
     */
    abstract protected function getSequenceHandler(): SequenceHandlerInterface;

    /**
     * @param Context $context
     * @return mixed
     * @throws HandlerException
     * @throws PredicateException
     */
    abstract protected function performExtract(Context $context);
}
