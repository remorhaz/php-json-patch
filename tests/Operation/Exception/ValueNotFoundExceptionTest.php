<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Patch\Operation\Exception\ValueNotFoundException;

#[CoversClass(ValueNotFoundException::class)]
class ValueNotFoundExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new ValueNotFoundException(1);
        self::assertSame(
            'Operation #1: value not found in \'value\' property',
            $exception->getMessage(),
        );
    }

    public function testGetIndex_ConstructedWithIndex_ReturnsSameValue(): void
    {
        $exception = new ValueNotFoundException(1);
        self::assertSame(1, $exception->getIndex());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new ValueNotFoundException(1);
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new ValueNotFoundException(1);
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new ValueNotFoundException(1, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
