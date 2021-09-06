<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace tests\Jojo1981\DataResolver\Predicate;

use Jojo1981\DataResolver\Handler\Exception\HandlerException;
use Jojo1981\DataResolver\Handler\SequenceHandlerInterface;
use Jojo1981\DataResolver\Predicate\IsEmptyPredicate;
use Jojo1981\DataResolver\Resolver\Context;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\RecursionContext\InvalidArgumentException as SebastianBergmannInvalidArgumentException;
use stdClass;

/**
 * @package tests\Jojo1981\DataResolver\Predicate
 */
final class IsEmptyPredicateTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|SequenceHandlerInterface */
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
     * @test
     *
     * @return void
     * @throws HandlerException
     * @throws ObjectProphecyException
     * @throws SebastianBergmannInvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function matchShouldUseSequenceHandlerWhenItSupportTheDataAndReturnItsResult(): void
    {
        $this->sequenceHandler->supports('text1')->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler->count('text1')->willReturn(0)->shouldBeCalledOnce();
        $this->sequenceHandler->supports('text2')->willReturn(true)->shouldBeCalledOnce();
        $this->sequenceHandler->count('text2')->willReturn(1)->shouldBeCalledOnce();

        $this->assertEquals(true, $this->getIsEmptyPredicate()->match(new Context('text1')));
        $this->assertEquals(false, $this->getIsEmptyPredicate()->match(new Context('text2')));
    }

    /**
     * @test
     * @dataProvider getTestData
     *
     * @param mixed $data
     * @param bool $expected
     * @return void
     * @throws HandlerException
     * @throws ExpectationFailedException
     * @throws SebastianBergmannInvalidArgumentException
     * @throws ObjectProphecyException
     */
    public function matchShouldReturnTheCorrectResult($data, bool $expected): void
    {
        $this->sequenceHandler->supports(Argument::any())->willReturn(false);
        $this->assertEquals($expected, $this->getIsEmptyPredicate()->match(new Context($data)));
    }

    /**
     * @return IsEmptyPredicate
     * @throws ObjectProphecyException
     */
    private function getIsEmptyPredicate(): IsEmptyPredicate
    {
        return new IsEmptyPredicate($this->sequenceHandler->reveal());
    }

    /**
     * @return array[]
     */
    public function getTestData(): array
    {
        return [
            [null, true],
            [0, true],
            [0.0, true],
            [[], true],
            [false, true],
            ['', true],
            ['0', true],
            ['0.0', false],
            [[2], false],
            [true, false],
            ['test', false],
            [new stdClass(), false]
        ];
    }
}
