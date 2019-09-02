<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Handler\MergeHandler;

use Jojo1981\DataResolver\Handler\MergeHandler\DefaultMergeHandler;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @package tests\Jojo1981\DataResolver\Handler\MergeHandler
 */
class DefaultMergeHandlerTest extends TestCase
{
    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function mergeWithContextDataNullWithEmptyElementShouldReturnEmptyStdClass(): void
    {
        $result = $this->getDefaultMergeHandler()->merge(new Context(null, ''), []);
        $this->assertEquals(new \stdClass(), $result);
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function mergeWithContextDataArrayWithEmptyElementShouldReturnAnEmptyArray(): void
    {
        $result = $this->getDefaultMergeHandler()->merge(new Context([], ''), []);
        $this->assertEquals([], $result);
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function mergeWithContextDataObjectWithElementsShouldReturnNonEmptyStdClass(): void
    {
        $expected = new \stdClass();
        $expected->id = 'id1';
        $expected->name = 10;
        $expected->list = [1, 7, 8];

        $result = $this->getDefaultMergeHandler()->merge(new Context(new \stdClass(), ''), $this->getElementsTestData());
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @return void
     */
    public function mergeWithContextDataArrayWithElementsShouldReturnNonEmptyAssociativeArray(): void
    {
        $expected = [
            'id' => 'id1',
            'name' => 10,
            'list' => [1, 7, 8]
        ];

        $result = $this->getDefaultMergeHandler()->merge(new Context([], ''), $this->getElementsTestData());
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    private function getElementsTestData(): array
    {
        return [
            'id' => 'id1',
            'name' => 10,
            'list' => [1, 7, 8]
        ];
    }

    /**
     * @return DefaultMergeHandler
     */
    private function getDefaultMergeHandler(): DefaultMergeHandler
    {
        return new DefaultMergeHandler();
    }
}