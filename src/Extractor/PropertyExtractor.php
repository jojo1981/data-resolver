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
use Jojo1981\DataResolver\Handler\MergeHandlerInterface;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use Jojo1981\DataResolver\Resolver\Context;
use function array_shift;
use function count;
use function get_class;
use function sprintf;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
final class PropertyExtractor implements ExtractorInterface
{
    /** @var NamingStrategyInterface */
    private NamingStrategyInterface $namingStrategy;

    /** @var PropertyHandlerInterface */
    private PropertyHandlerInterface $propertyHandler;

    /** @var MergeHandlerInterface */
    private MergeHandlerInterface $mergeHandlerInterface;

    /** @var string[] */
    private array $propertyNames;

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
     * @return mixed
     * @throws ExtractorException
     * @throws HandlerException
     */
    public function extract(Context $context)
    {
        $elements = [];
        foreach ($this->propertyNames as $propertyName) {
            if (false === $this->canExtract($context->getData(), $propertyName)) {
                throw new ExtractorException(sprintf(
                    'Could not extract data with `%s` for property: `%s` at path: `%s`',
                    get_class($this),
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

        if (1 === count($elements)) {
            return array_shift($elements);
        }

        return $this->mergeHandlerInterface->merge($context, $elements);
    }

    /**
     * @param mixed $data
     * @param string $propertyName
     * @return bool
     * @throws HandlerException
     */
    private function canExtract($data, string $propertyName): bool
    {
        return $this->propertyHandler->supports($propertyName, $data)
            && $this->propertyHandler->hasValueForPropertyName($this->namingStrategy, $propertyName, $data);
    }
}
