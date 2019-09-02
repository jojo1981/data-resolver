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
use Jojo1981\DataResolver\Handler\MergeHandlerInterface;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
class PropertyExtractor implements ExtractorInterface
{
    /** @var NamingStrategyInterface */
    private $namingStrategy;

    /** @var PropertyHandlerInterface */
    private $propertyHandler;

    /** @var MergeHandlerInterface */
    private $mergeHandlerInterface;

    /** @var string[] */
    private $propertyNames;

    /**
     * @param NamingStrategyInterface $namingStrategy
     * @param PropertyHandlerInterface $propertyHandler
     * @param MergeHandlerInterface $mergeHandlerInterface
     * @param string[] $propertyNames
     */
    public function __construct(
        NamingStrategyInterface $namingStrategy,
        PropertyHandlerInterface $propertyHandler,
        MergeHandlerInterface $mergeHandlerInterface,
        array $propertyNames
    ) {
        $this->namingStrategy = $namingStrategy;
        $this->propertyHandler = $propertyHandler;
        $this->mergeHandlerInterface = $mergeHandlerInterface;
        $this->propertyNames = $propertyNames;
    }

    /**
     * @param Context $context
     * @throws HandlerException
     * @throws ExtractorException
     * @return mixed
     */
    public function extract(Context $context)
    {
        $elements = [];
        foreach ($this->propertyNames as $propertyName) {
            if (false === $this->canExtract($context->getData(), $propertyName)) {
                throw new ExtractorException(\sprintf(
                    'Could not extract data with `%s` for property: `%s` at path: `%s`',
                    \get_class($this),
                    $propertyName,
                    $context->getPath()
                ));
            }

            $context->pushPathPart($propertyName);
            $elements[$propertyName] = $this->propertyHandler->getValueForPropertyName(
                $this->namingStrategy,
                $propertyName,
                $context->getData()
            );
            $context->popPathPart();
        }

        if (1 === \count($elements)) {
            return \array_shift($elements);
        }

        return $this->mergeHandlerInterface->merge($context, $elements);
    }

    /**
     * @param mixed $data
     * @param string $propertyName
     * @throws HandlerException
     * @return bool
     */
    private function canExtract($data, string $propertyName): bool
    {
        return $this->propertyHandler->supports($propertyName, $data)
            && $this->propertyHandler->hasValueForPropertyName($this->namingStrategy, $propertyName, $data);
    }
}