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

use Exception;
use Jojo1981\DataResolver\Handler\PropertyHandlerInterface;
use Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
final class HasPropertyExtractor implements ExtractorInterface
{
    /** @var PropertyHandlerInterface */
    private PropertyHandlerInterface $propertyHandler;

    /** @var NamingStrategyInterface */
    private NamingStrategyInterface $namingStrategy;

    /** @var string */
    private string $propertyName;

    /**
     * @param PropertyHandlerInterface $propertyHandler
     * @param NamingStrategyInterface $namingStrategy
     * @param string $propertyName
     */
    public function __construct(
        PropertyHandlerInterface $propertyHandler,
        NamingStrategyInterface $namingStrategy,
        string $propertyName
    ) {
        $this->propertyHandler = $propertyHandler;
        $this->namingStrategy = $namingStrategy;
        $this->propertyName = $propertyName;
    }

    /**
     * @param Context $context
     * @return bool
     */
    public function extract(Context $context): bool
    {
        $data = $context->getData();
        if (!$this->propertyHandler->supports($this->propertyName, $data)) {
            return false;
        }

        try {
            return $this->propertyHandler->hasValueForPropertyName(
                $this->namingStrategy,
                $this->propertyName,
                $data
            );
        } catch (Exception $exception) {
            return false;
        }
    }
}
