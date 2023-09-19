<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Patch\Operation\Exception\InvalidPathException;

/**
 * @covers \Remorhaz\JSON\Patch\Operation\Exception\InvalidPathException
 */
class InvalidPathExceptionTest extends TestCase
{
    public function testGetIndex_ConstructedWithIndex_ReturnsSameValue(): void
    {
        $exception = new InvalidPathException(1, 'a', 2);
        self::assertSame(1, $exception->getIndex());
    }

    public function testGetProperty_ConstructedWithProperty_ReturnsSameValue(): void
    {
        $exception = new InvalidPathException(1, 'a', 2);
        self::assertSame('a', $exception->getProperty());
    }

    public function testGetPath_ConstructedWithPath_ReturnsSameValue(): void
    {
        $exception = new InvalidPathException(1, 'a', 2);
        self::assertSame(2, $exception->getPath());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new InvalidPathException(1, 'a', 2);
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new InvalidPathException(1, 'a', 2);
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new InvalidPathException(1, 'a', 2, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
