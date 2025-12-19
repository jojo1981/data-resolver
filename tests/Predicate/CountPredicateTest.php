<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
declare(strict_types=1);

namespace tests\Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\CountPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException as PHPUnitExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
final class CountPredicateTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<SequenceHandlerInterface> */
    private ObjectProphecy $sequenceHandler;

    /**
     * @return void
     * @throws InterfaceNotFoundException
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    protected function setUp(): void
    {
        $this->sequenceHandler = $this->prophesize(SequenceHandlerInterface::class);
    }

    /**
     * @return void
     * @throws HandlerException
     * @throws PHPUnitExpectationFailedException
     * @throws ObjectProphecyException
     */
    public function testMatchShouldReturnTheCorrectResult(): void
    {
        $this->sequenceHandler->count('text1')->willReturn(1)->shouldBeCalledOnce();
        $this->sequenceHandler->count('text2')->willReturn(4)->shouldBeCalledOnce();
        self::assertTrue($this->getIsEmptyPredicate()->match(new Context('text1')));
        self::assertFalse($this->getIsEmptyPredicate()->match(new Context('text2')));
    }

    /**
     * @return CountPredicate
     * @throws ObjectProphecyException
     */
    private function getIsEmptyPredicate(): CountPredicate
    {
        return new CountPredicate($this->sequenceHandler->reveal(), 1);
    }
}
