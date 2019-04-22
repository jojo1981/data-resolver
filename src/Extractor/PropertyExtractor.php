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
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
class PropertyExtractor implements ExtractorInterface
{
    /** @var PropertyHandlerInterface */
    private $propertyHandler;

    /** @var string */
    private $propertyName;

    /**
     * @param PropertyHandlerInterface $propertyHandler
     * @param string $propertyName
     */
    public function __construct(PropertyHandlerInterface $propertyHandler, string $propertyName)
    {
        $this->propertyHandler = $propertyHandler;
        $this->propertyName = $propertyName;
    }

    /**
     * @return string
     */
    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    /**
     * @param Context $context
     * @throws HandlerException
     * @throws ExtractorException
     * @return mixed
     */
    public function extract(Context $context)
    {
        if (
            !$this->propertyHandler->supports($this->propertyName, $context->getData()) ||
            !$this->propertyHandler->hasValueForPropertyName($this->propertyName, $context->getData())
        ) {
            throw new ExtractorException(\sprintf(
                'Could not extract data with `%s` for property: `%s` at path: `%s`',
                \get_class($this),
                $this->propertyName,
                $context->getPath()
            ));
        }
        $context->pushPathPart($this->propertyName);

        return $this->propertyHandler->getValueForPropertyName($this->propertyName, $context->getData());
    }
}