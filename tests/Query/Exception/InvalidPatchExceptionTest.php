<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Query\Exception\InvalidPatchException;

#[CoversClass(InvalidPatchException::class)]
class InvalidPatchExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new InvalidPatchException(
            $this->createMock(NodeValueInterface::class),
        );
        self::assertSame('Patch must be an array', $exception->getMessage());
    }

    public function testGetPatch_ConstructedWithPatch_ReturnsSameInstance(): void
    {
        $patch = $this->createMock(NodeValueInterface::class);
        $exception = new InvalidPatchException($patch);
        self::assertSame($patch, $exception->getPatch());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new InvalidPatchException(
            $this->createMock(NodeValueInterface::class),
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new InvalidPatchException(
            $this->createMock(NodeValueInterface::class),
            $previous,
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
