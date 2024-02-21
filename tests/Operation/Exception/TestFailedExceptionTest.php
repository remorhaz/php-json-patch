<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Operation\Exception\TestFailedException;

#[CoversClass(TestFailedException::class)]
class TestFailedExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new TestFailedException(
            1,
            $this->createMock(NodeValueInterface::class),
            'a',
            $this->createMock(NodeValueInterface::class),
        );
        self::assertSame('Operation #1: test operation failed at \'a\'', $exception->getMessage());
    }

    public function testGetIndex_ConstructedWithIndex_ReturnsSameValue(): void
    {
        $exception = new TestFailedException(
            1,
            $this->createMock(NodeValueInterface::class),
            'a',
            $this->createMock(NodeValueInterface::class),
        );
        self::assertSame(1, $exception->getIndex());
    }

    public function testGetDocument_ConstructedWithDocument_ReturnsSameInstance(): void
    {
        $document = $this->createMock(NodeValueInterface::class);
        $exception = new TestFailedException(
            1,
            $document,
            'a',
            $this->createMock(NodeValueInterface::class),
        );
        self::assertSame($document, $exception->getDocument());
    }

    public function testGetPath_ConstructedWithPath_ReturnsSameValue(): void
    {
        $exception = new TestFailedException(
            1,
            $this->createMock(NodeValueInterface::class),
            'a',
            $this->createMock(NodeValueInterface::class),
        );
        self::assertSame('a', $exception->getPath());
    }

    public function testGetExpectedValue_ConstructedWithExpectedValue_ReturnsSameInstance(): void
    {
        $expectedValue = $this->createMock(NodeValueInterface::class);
        $exception = new TestFailedException(
            1,
            $this->createMock(NodeValueInterface::class),
            'a',
            $expectedValue,
        );
        self::assertSame($expectedValue, $exception->getExpectedValue());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new TestFailedException(
            1,
            $this->createMock(NodeValueInterface::class),
            'a',
            $this->createMock(NodeValueInterface::class),
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new TestFailedException(
            1,
            $this->createMock(NodeValueInterface::class),
            'a',
            $this->createMock(NodeValueInterface::class),
            $previous,
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
