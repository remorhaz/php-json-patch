<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Query;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Exception\ExceptionInterface as PatchExceptionInterface;
use Remorhaz\JSON\Patch\Operation\OperationInterface;
use Remorhaz\JSON\Patch\Query\Query;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;

#[CoversClass(Query::class)]
class QueryTest extends TestCase
{
    /**
     * @throws PatchExceptionInterface
     */
    public function testInvoke_ConstructedWithEncoder_UsesSameInstanceOnResultEncode(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $query = new Query(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
        );
        $result = $query(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(PointerProcessorInterface::class),
        );
        $encoder
            ->expects(self::once())
            ->method('exportValue');
        $result->encode();
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testInvoke_ConstructedWithDecoder_UsesSameInstanceOnResultDecode(): void
    {
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $query = new Query(
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
        );
        $result = $query(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(PointerProcessorInterface::class),
        );
        $decoder
            ->expects(self::once())
            ->method('exportValue');
        $result->decode();
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testInvoke_ConstructedWithoutOperations_ReturnsInputInResult(): void
    {
        $query = new Query(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
        );
        $input = $this->createMock(NodeValueInterface::class);
        $result = $query(
            $input,
            $this->createMock(PointerProcessorInterface::class),
        );
        self::assertSame($input, $result->get());
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testInvoke_ConstructedWithSingleOperation_PassesInputToOperation(): void
    {
        $operation = $this->createMock(OperationInterface::class);
        $query = new Query(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $operation,
        );
        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);
        $operation
            ->expects(self::once())
            ->method('apply')
            ->with(self::identicalTo($input), self::identicalTo($pointerProcessor));
        $query($input, $pointerProcessor);
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testInvoke_ConstructedWithTwoOperations_PassesInputToFirstOperation(): void
    {
        $operation = $this->createMock(OperationInterface::class);
        $query = new Query(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $operation,
            $this->createMock(OperationInterface::class),
        );
        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);
        $operation
            ->expects(self::once())
            ->method('apply')
            ->with(self::identicalTo($input), self::identicalTo($pointerProcessor));
        $query($input, $pointerProcessor);
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testInvoke_ConstructedWithTwoOperations_PassesFirstOperationResultToSecondOperation(): void
    {
        $firstOperation = $this->createMock(OperationInterface::class);
        $secondOperation = $this->createMock(OperationInterface::class);
        $query = new Query(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $firstOperation,
            $secondOperation,
        );
        $firstOperationResult = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);
        $firstOperation
            ->method('apply')
            ->willReturn($firstOperationResult);
        $secondOperation
            ->expects(self::once())
            ->method('apply')
            ->with(self::identicalTo($firstOperationResult), self::identicalTo($pointerProcessor));
        $query($firstOperationResult, $pointerProcessor);
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testInvoke_ConstructedWithSingleOperation_ReturnsOperationApplicationInResult(): void
    {
        $operation = $this->createMock(OperationInterface::class);
        $query = new Query(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $operation,
        );
        $output = $this->createMock(NodeValueInterface::class);
        $operation
            ->method('apply')
            ->willReturn($output);
        $result = $query(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(PointerProcessorInterface::class),
        );
        self::assertSame($output, $result->get());
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testInvoke_ConstructedWithTwoOperations_ReturnsLastOperationApplicationInResult(): void
    {
        $operation = $this->createMock(OperationInterface::class);
        $query = new Query(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(OperationInterface::class),
            $operation,
        );
        $output = $this->createMock(NodeValueInterface::class);
        $operation
            ->method('apply')
            ->willReturn($output);
        $result = $query(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(PointerProcessorInterface::class),
        );
        self::assertSame($output, $result->get());
    }
}
