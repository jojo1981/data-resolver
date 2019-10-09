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
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
class StringLengthExtractor implements ExtractorInterface
{
    /**
     * @param Context $context
     * @throws ExtractorException
     * @return int
     */
    public function extract(Context $context): int
    {
        $data = $context->getData();
        if (!\is_string($data)) {
            throw new ExtractorException(\sprintf(
                'Could not extract data with `%s` at path: `%s`. Data is not of type string, but of type: %s',
                \get_class($this),
                $context->getPath(),
                \is_object($data) ? \get_class($data) : \gettype($data)
            ));
        }

        return \strlen($data);
    }
}