<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Operation\CopyOperation;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Processor\Result\ResultInterface as PointerResultInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface as PointerQueryInterface;

/**
 * @covers \Remorhaz\JSON\Patch\Operation\CopyOperation
 */
class CopyOperationTest extends TestCase
{
    public function testApply_Constructed_PassesInputToPointerProcessorSelect(): void
    {
        $fromPointer = $this->createMock(PointerQueryInterface::class);
        $operation = new CopyOperation(
            0,
            $this->createMock(PointerQueryInterface::class),
            $fromPointer,
        );

        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $pointerProcessor
            ->expects(self::once())
            ->method('select')
            ->with(self::identicalTo($fromPointer), self::identicalTo($input));
        $operation->apply($input, $pointerProcessor);
    }

    public function testApply_PointerProcessorSelectsValue_PassesSelectedValueToPointerProcessorAdd(): void
    {
        $pathPointer = $this->createMock(PointerQueryInterface::class);
        $operation = new CopyOperation(
            0,
            $pathPointer,
            $this->createMock(PointerQueryInterface::class),
        );

        $input = $this->createMock(NodeValueInterface::class);
        $selectedValue = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $result = $this->createMock(PointerResultInterface::class);
        $result
            ->method('get')
            ->willReturn($selectedValue);
        $pointerProcessor
            ->method('select')
            ->willReturn($result);
        $pointerProcessor
            ->expects(self::once())
            ->method('add')
            ->with(self::identicalTo($pathPointer), self::identicalTo($input), self::identicalTo($selectedValue));
        $operation->apply($input, $pointerProcessor);
    }

    public function testApply_PointerProcessorAddsValue_ReturnsValueFromAddResult(): void
    {
        $operation = new CopyOperation(
            0,
            $this->createMock(PointerQueryInterface::class),
            $this->createMock(PointerQueryInterface::class)
        );

        $input = $this->createMock(NodeValueInterface::class);
        $addedValue = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $result = $this->createMock(PointerResultInterface::class);
        $result
            ->method('get')
            ->willReturn($addedValue);
        $pointerProcessor
            ->method('add')
            ->willReturn($result);
        $actualResult = $operation->apply($input, $pointerProcessor);
        self::assertSame($addedValue, $actualResult);
    }
}
