<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Extractor;

use Jojo1981\DataResolver\Resolver\Context;
use function call_user_func;

/**
 * @package Jojo1981\DataResolver\Extractor
 */
class CallbackExtractor implements ExtractorInterface
{
    /** @var callable */
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param Context $context
     * @return mixed
     */
    public function extract(Context $context)
    {
        return call_user_func($this->callback, $context->copy()->getData());
    }
}
