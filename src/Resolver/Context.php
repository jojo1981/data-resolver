<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Resolver;

use function array_pop;
use function explode;
use function implode;
use function strpos;

/**
 * @package Jojo1981\DataResolver\Resolver
 */
class Context
{
    /** @var mixed */
    private $data;

    /** @var string[] */
    private $pathParts;

    /**
     * @param mixed $data
     * @param string $path
     */
    public function __construct($data, string $path = '')
    {
        $this->data = $data;
        $this->setPath($path);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return implode('.', $this->pathParts);
    }

    /**
     * @param string $pathPart
     * @return $this
     */
    public function pushPathPart(string $pathPart): self
    {
        $this->pathParts[] = $pathPart;

        return $this;
    }

    /**
     * @return $this
     */
    public function popPathPart(): self
    {
        array_pop($this->pathParts);

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->pathParts = false !== strpos($path, '.') ? explode('.', $path) : [];

        return $this;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return Context
     */
    public function copy(): Context
    {
        return clone $this;
    }
}
