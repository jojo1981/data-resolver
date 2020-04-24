<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use function trait_exists;

if (trait_exists('\Prophecy\PhpUnit\ProphecyTrait')) {

    /**
     * @package tests\Jojo1981\DataResolver
     */
    abstract class TestCase extends BaseTestCase
    {
        use ProphecyTrait;
    }
} else {

    /**
     * @package tests\Jojo1981\DataResolver
     */
    abstract class TestCase extends BaseTestCase
    {
    }
}
