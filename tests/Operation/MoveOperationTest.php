<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Operation\MoveOperation;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Processor\Result\ResultInterface as PointerResultInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface as PointerQueryInterface;

/**
 * @covers \Remorhaz\JSON\Patch\Operation\MoveOperation
 */
class MoveOperationTest extends TestCase
{
    public function testApply_Constructed_PassesInputToPointerProcessorSelect(): void
    {
        $fromPointer = $this->createMock(PointerQueryInterface::class);
        $operation = new MoveOperation(
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

    public function testApply_Constructed_PassesInputToPointerProcessorDelete(): void
    {
        $fromPointer = $this->createMock(PointerQueryInterface::class);
        $operation = new MoveOperation(
            0,
            $this->createMock(PointerQueryInterface::class),
            $fromPointer,
        );

        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $pointerProcessor
            ->expects(self::once())
            ->method('delete')
            ->with(self::identicalTo($fromPointer), self::identicalTo($input));
        $operation->apply($input, $pointerProcessor);
    }

    public function testApply_PointerProcessorSelectsAndDeletesValues_PassesProcessedValuesToPointerProcessor(): void
    {
        $pathPointer = $this->createMock(PointerQueryInterface::class);
        $operation = new MoveOperation(
            0,
            $pathPointer,
            $this->createMock(PointerQueryInterface::class),
        );

        $input = $this->createMock(NodeValueInterface::class);
        $deletedValue = $this->createMock(NodeValueInterface::class);
        $selectedValue = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $selectResult = $this->createMock(PointerResultInterface::class);
        $selectResult
            ->method('get')
            ->willReturn($selectedValue);
        $deleteResult = $this->createMock(PointerResultInterface::class);
        $deleteResult
            ->method('get')
            ->willReturn($deletedValue);
        $pointerProcessor
            ->method('select')
            ->willReturn($selectResult);
        $pointerProcessor
            ->method('delete')
            ->willReturn($deleteResult);
        $pointerProcessor
            ->expects(self::once())
            ->method('add')
            ->with(
                self::identicalTo($pathPointer),
                self::identicalTo($deletedValue),
                self::identicalTo($selectedValue),
            );
        $operation->apply($input, $pointerProcessor);
    }

    public function testApply_PointerProcessorAddsValues_ReturnsValueFromAddedResult(): void
    {
        $pathPointer = $this->createMock(PointerQueryInterface::class);
        $operation = new MoveOperation(
            0,
            $pathPointer,
            $this->createMock(PointerQueryInterface::class)
        );

        $input = $this->createMock(NodeValueInterface::class);
        $addedValue = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $addResult = $this->createMock(PointerResultInterface::class);
        $addResult
            ->method('get')
            ->willReturn($addedValue);
        $pointerProcessor
            ->method('add')
            ->willReturn($addResult);
        $actualValue = $operation->apply($input, $pointerProcessor);
        self::assertSame($addedValue, $actualValue);
    }
}
