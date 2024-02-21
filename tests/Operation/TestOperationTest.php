<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Comparator\ComparatorInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Operation\Exception\TestFailedException;
use Remorhaz\JSON\Patch\Operation\TestOperation;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Processor\Result\ResultInterface as PointerResultInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface as PointerQueryInterface;

#[CoversClass(TestOperation::class)]
class TestOperationTest extends TestCase
{
    public function testApply_Constructed_PassesInputToPointerProcessor(): void
    {
        $pathPointer = $this->createMock(PointerQueryInterface::class);
        $value = $this->createMock(NodeValueInterface::class);
        $comparator = $this->createMock(ComparatorInterface::class);
        $operation = new TestOperation(0, $pathPointer, $value, $comparator);
        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $result = $this->createMock(PointerResultInterface::class);
        $result
            ->method('exists')
            ->willReturn(true);
        $comparator
            ->method('compare')
            ->willReturn(true);

        $pointerProcessor
            ->expects(self::once())
            ->method('select')
            ->with(self::identicalTo($pathPointer), self::identicalTo($input))
            ->willReturn($result);
        $operation->apply($input, $pointerProcessor);
    }

    public function testApply_PointerProcessorReturnsExistingResult_PassesValueFromResultToComparator(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $comparator = $this->createMock(ComparatorInterface::class);
        $operation = new TestOperation(
            0,
            $this->createMock(PointerQueryInterface::class),
            $value,
            $comparator,
        );
        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $resultValue = $this->createMock(NodeValueInterface::class);
        $result = $this->createMock(PointerResultInterface::class);
        $result
            ->method('exists')
            ->willReturn(true);
        $result
            ->method('get')
            ->willReturn($resultValue);
        $comparator
            ->method('compare')
            ->willReturn(true);
        $pointerProcessor
            ->method('select')
            ->willReturn($result);

        $comparator
            ->expects(self::once())
            ->method('compare')
            ->with(self::identicalTo($resultValue), self::identicalTo($value));
        $operation->apply($input, $pointerProcessor);
    }

    public function testApply_PointerProcessorReturnsNonExistingResult_ThrowsException(): void
    {
        $comparator = $this->createMock(ComparatorInterface::class);
        $operation = new TestOperation(
            0,
            $this->createMock(PointerQueryInterface::class),
            $this->createMock(NodeValueInterface::class),
            $comparator,
        );
        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $result = $this->createMock(PointerResultInterface::class);
        $result
            ->method('exists')
            ->willReturn(false);
        $comparator
            ->method('compare')
            ->willReturn(true);
        $pointerProcessor
            ->method('select')
            ->willReturn($result);

        $this->expectException(TestFailedException::class);
        $operation->apply($input, $pointerProcessor);
    }

    public function testApply_ComparatorFailsForExistingResult_ThrowsException(): void
    {
        $comparator = $this->createMock(ComparatorInterface::class);
        $operation = new TestOperation(
            0,
            $this->createMock(PointerQueryInterface::class),
            $this->createMock(NodeValueInterface::class),
            $comparator,
        );
        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $result = $this->createMock(PointerResultInterface::class);
        $result
            ->method('exists')
            ->willReturn(true);
        $comparator
            ->method('compare')
            ->willReturn(false);
        $pointerProcessor
            ->method('select')
            ->willReturn($result);

        $this->expectException(TestFailedException::class);
        $operation->apply($input, $pointerProcessor);
    }
}
