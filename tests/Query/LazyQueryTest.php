<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Query;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeArrayValue;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Exception\ExceptionInterface as PatchExceptionInterface;
use Remorhaz\JSON\Patch\Operation\OperationFactoryInterface;
use Remorhaz\JSON\Patch\Operation\OperationInterface;
use Remorhaz\JSON\Patch\Query\Exception\InvalidPatchException;
use Remorhaz\JSON\Patch\Query\Exception\OperationNotLoadedException;
use Remorhaz\JSON\Patch\Query\LazyQuery;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;

/**
 * Class LazyQueryTest
 *
 * @package Remorhaz\JSON\Patch\Query
 * @covers  \Remorhaz\JSON\Patch\Query\LazyQuery
 */
class LazyQueryTest extends TestCase
{

    /**
     * @throws PatchExceptionInterface
     */
    public function testInvoke_ConstructedWithNonArrayPatch_ThrowsException(): void
    {
        $query = new LazyQuery(
            $this->createMock(OperationFactoryInterface::class),
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $this->expectException(InvalidPatchException::class);
        $query($input, $pointerProcessor);
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testInvoke_OperationFactoryThrowsException_ThrowsException(): void
    {
        $operationFactory = $this->createMock(OperationFactoryInterface::class);
        $patch = new NodeArrayValue(
            [
                (object) [],
            ],
            new Path,
            NodeValueFactory::create()
        );
        $query = new LazyQuery(
            $operationFactory,
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $patch
        );
        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $operationFactory
            ->method('fromJson')
            ->willThrowException(new Exception);
        $this->expectException(OperationNotLoadedException::class);
        $query($input, $pointerProcessor);
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testInvoke_OperationFactoryReturnsTwoOperations_SameOperationsAppliedInSameOrder(): void
    {
        $operationFactory = $this->createMock(OperationFactoryInterface::class);
        $patch = new NodeArrayValue(
            [
                (object) [],
                (object) [],
            ],
            new Path,
            NodeValueFactory::create()
        );
        $query = new LazyQuery(
            $operationFactory,
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $patch
        );
        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);

        $firstOperation = $this->createMock(OperationInterface::class);
        $secondOperation = $this->createMock(OperationInterface::class);
        $operationFactory
            ->expects(self::exactly(2))
            ->method('fromJson')
            ->willReturnOnConsecutiveCalls($firstOperation, $secondOperation);

        $firstOutput = $this->createMock(NodeValueInterface::class);
        $firstOperation
            ->method('apply')
            ->with(self::identicalTo($input), self::identicalTo($pointerProcessor))
            ->willReturn($firstOutput);

        $secondOutput = $this->createMock(NodeValueInterface::class);
        $secondOperation
            ->method('apply')
            ->with(self::identicalTo($firstOutput), self::identicalTo($pointerProcessor))
            ->willReturn($secondOutput);
        $actualValue = $query($input, $pointerProcessor);
        self::assertSame($secondOutput, $actualValue->get());
    }
}
