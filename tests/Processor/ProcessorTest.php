<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Processor;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Processor\Exception\ExceptionInterface as ProcessorExceptionInterface;
use Remorhaz\JSON\Patch\Processor\Exception\PatchNotAppliedException;
use Remorhaz\JSON\Patch\Processor\Processor;
use Remorhaz\JSON\Patch\Query\QueryInterface;
use Remorhaz\JSON\Patch\Result\ResultInterface;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;

/**
 * @covers \Remorhaz\JSON\Patch\Processor\Processor
 */
class ProcessorTest extends TestCase
{
    public function testCreate_Always_ReturnsProcessorInstance(): void
    {
        self::assertInstanceOf(Processor::class, Processor::create());
    }

    /**
     * @throws ProcessorExceptionInterface
     */
    public function testApply_QueryThrowsException_ThrowsException(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('__invoke')
            ->willThrowException(new Exception());
        $processor = Processor::create();
        $data = $this->createMock(NodeValueInterface::class);
        $this->expectException(PatchNotAppliedException::class);
        $processor->apply($query, $data);
    }

    /**
     * @throws ProcessorExceptionInterface
     */
    public function testApply_QueryReturnsResult_ReturnsSameInstance(): void
    {
        $result = $this->createMock(ResultInterface::class);
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('__invoke')
            ->willReturn($result);
        $actualValue = Processor::create()
            ->apply($query, $this->createMock(NodeValueInterface::class));
        self::assertSame($result, $actualValue);
    }

    /**
     * @throws ProcessorExceptionInterface
     */
    public function testApply_ConstructedWithPointerProcessor_PassesSameInstanceToQuery(): void
    {
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);
        $processor = new Processor($pointerProcessor);
        $data = $this->createMock(NodeValueInterface::class);
        $query = $this->createMock(QueryInterface::class);
        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with(self::identicalTo($data), self::identicalTo($pointerProcessor));
        $processor->apply($query, $data);
    }
}
