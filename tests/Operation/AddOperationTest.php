<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Operation\AddOperation;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Processor\Result\ResultInterface as PointerResultInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface as PointerQueryInterface;

/**
 * @covers \Remorhaz\JSON\Patch\Operation\AddOperation
 */
class AddOperationTest extends TestCase
{
    public function testApply_Constructed_PassesInputToGivenPointerProcessor(): void
    {
        $pathPointer = $this->createMock(PointerQueryInterface::class);
        $value = $this->createMock(NodeValueInterface::class);
        $operation = new AddOperation(0, $pathPointer, $value);

        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);
        $pointerProcessor
            ->expects(self::once())
            ->method('add')
            ->with(
                self::identicalTo($pathPointer),
                self::identicalTo($input),
                self::identicalTo($value),
            );
        $operation->apply($input, $pointerProcessor);
    }

    public function testApply_Constructed_ReturnsValueFromPointerProcessor(): void
    {
        $operation = new AddOperation(
            0,
            $this->createMock(PointerQueryInterface::class),
            $this->createMock(NodeValueInterface::class),
        );

        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);
        $processedValue = $this->createMock(NodeValueInterface::class);
        $result = $this->createMock(PointerResultInterface::class);
        $result
            ->method('get')
            ->willReturn($processedValue);
        $pointerProcessor
            ->method('add')
            ->willReturn($result);
        $actualValue = $operation->apply($input, $pointerProcessor);
        self::assertSame($processedValue, $actualValue);
    }
}
