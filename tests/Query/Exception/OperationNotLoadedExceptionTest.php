<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Query\Exception\OperationNotLoadedException;

#[CoversClass(OperationNotLoadedException::class)]
class OperationNotLoadedExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new OperationNotLoadedException(
            1,
            $this->createMock(NodeValueInterface::class),
        );
        self::assertSame('Failed to load operation #1 from patch', $exception->getMessage());
    }

    public function testGetIndex_ConstructedWithIndex_ReturnsSameValue(): void
    {
        $exception = new OperationNotLoadedException(
            1,
            $this->createMock(NodeValueInterface::class),
        );
        self::assertSame(1, $exception->getIndex());
    }

    public function testGetPatch_ConstructedWithPatch_ReturnsSameInstance(): void
    {
        $patch = $this->createMock(NodeValueInterface::class);
        $exception = new OperationNotLoadedException(1, $patch);
        self::assertSame($patch, $exception->getPatch());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new OperationNotLoadedException(
            1,
            $this->createMock(NodeValueInterface::class),
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new OperationNotLoadedException(
            1,
            $this->createMock(NodeValueInterface::class),
            $previous,
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
