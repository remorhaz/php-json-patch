<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Operation\ReplaceOperation;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Processor\Result\ResultInterface as PointerResultInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface as PointerQueryInterface;

#[CoversClass(ReplaceOperation::class)]
class ReplaceOperationTest extends TestCase
{
    public function testApply_Constructed_PassesInputToGivenPointerProcessor(): void
    {
        $pathPointer = $this->createMock(PointerQueryInterface::class);
        $value = $this->createMock(NodeValueInterface::class);
        $operation = new ReplaceOperation(0, $pathPointer, $value);

        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);
        $pointerProcessor
            ->expects(self::once())
            ->method('replace')
            ->with(
                self::identicalTo($pathPointer),
                self::identicalTo($input),
                self::identicalTo($value),
            );
        $operation->apply($input, $pointerProcessor);
    }

    public function testApply_Constructed_ReturnsValueFromPointerProcessor(): void
    {
        $operation = new ReplaceOperation(
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
            ->method('replace')
            ->willReturn($result);
        $actualValue = $operation->apply($input, $pointerProcessor);
        self::assertSame($processedValue, $actualValue);
    }
}
