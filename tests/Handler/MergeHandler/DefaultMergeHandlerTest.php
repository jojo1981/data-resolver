<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
declare(strict_types=1);

namespace tests\Jojo1981\DataResolver\Handler\MergeHandler;

use Jojo1981\DataResolver\Handler\MergeHandler\DefaultMergeHandler;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @package tests\Jojo1981\DataResolver\Handler\MergeHandler
 */
final class DefaultMergeHandlerTest extends TestCase
{
    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testMergeWithContextDataNullWithEmptyElementShouldReturnEmptyArray(): void
    {
        $result = $this->getDefaultMergeHandler()->merge(new Context(null, ''), []);
        self::assertEquals([], $result);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testMergeWithContextDataArrayWithEmptyElementShouldReturnAnEmptyArray(): void
    {
        $result = $this->getDefaultMergeHandler()->merge(new Context([], ''), []);
        self::assertEquals([], $result);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testMergeWithContextDataObjectWithElementsShouldReturnNonEmptyStdClass(): void
    {
        $expected = new stdClass();
        $expected->id = 'id1';
        $expected->name = 10;
        $expected->list = [1, 7, 8];

        $result = $this->getDefaultMergeHandler()->merge(
            new Context(new stdClass(), ''),
            $this->getElementsTestData()
        );
        self::assertEquals($expected, $result);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testMergeWithContextDataArrayWithElementsShouldReturnNonEmptyAssociativeArray(): void
    {
        $expected = [
            'id' => 'id1',
            'name' => 10,
            'list' => [1, 7, 8]
        ];

        $result = $this->getDefaultMergeHandler()->merge(new Context([], ''), $this->getElementsTestData());
        self::assertEquals($expected, $result);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testMergeWithContextDataIndexedArrayWithElementsShouldReturnMergedIndexedArray(): void
    {
        $elements = [
            'primaryAddresses' => [
                [
                    'address1'
                ],
                [
                    'address2'
                ],
                [],
                [
                    'address3'
                ]
            ],
            'secondaryAddresses' => [
                [
                    'address4',
                ],
                [],
                [
                    'address5'
                ],
                [
                    'address6'
                ]
            ]
        ];

        $expected = [['address1'], ['address2'], ['address3'], ['address4'], ['address5'], ['address6']];

        $result = $this->getDefaultMergeHandler()->merge(new Context([], ''), $elements);
        self::assertEquals($expected, $result);
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
