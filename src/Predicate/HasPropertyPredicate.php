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

use Exception;
use Jojo1981\DataResolver\Extractor\HasPropertyExtractor;
use Jojo1981\DataResolver\Resolver\Context;

/**
 * @package Jojo1981\DataResolver\Predicate
 */
final class HasPropertyPredicate implements PredicateInterface
{
    /** @var HasPropertyExtractor */
    private HasPropertyExtractor $extractor;

    /**
     * @param HasPropertyExtractor $extractor
     */
    public function __construct(HasPropertyExtractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * @param Context $context
     * @return bool
     */
    public function match(Context $context): bool
    {
        try {
            return $this->extractor->extract($context);
        } catch (Exception $exception) {
            return false;
        }
    }
}
