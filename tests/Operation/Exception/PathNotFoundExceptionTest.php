<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Patch\Operation\Exception\PathNotFoundException;

/**
 * @covers \Remorhaz\JSON\Patch\Operation\Exception\PathNotFoundException
 */
class PathNotFoundExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new PathNotFoundException(1, 'a');
        self::assertSame(
            'Operation #1: JSON Pointer not found in \'a\' property',
            $exception->getMessage(),
        );
    }

    public function testGetIndex_ConstructedWithIndex_ReturnsSameValue(): void
    {
        $exception = new PathNotFoundException(1, 'a');
        self::assertSame(1, $exception->getIndex());
    }

    public function testGetProperty_ConstructedWithProperty_ReturnsSameValue(): void
    {
        $exception = new PathNotFoundException(1, 'a');
        self::assertSame('a', $exception->getProperty());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new PathNotFoundException(1, 'a');
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new PathNotFoundException(1, 'a');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new PathNotFoundException(1, 'a', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
