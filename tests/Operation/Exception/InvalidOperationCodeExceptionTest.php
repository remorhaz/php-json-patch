<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Patch\Operation\Exception\InvalidOperationCodeException;

/**
 * @covers \Remorhaz\JSON\Patch\Operation\Exception\InvalidOperationCodeException
 */
class InvalidOperationCodeExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new InvalidOperationCodeException(1, 2);
        self::assertSame(
            'Operation #1: operation code in \'op\' property must be a string',
            $exception->getMessage(),
        );
    }

    public function testGetIndex_ConstructedWithIndex_ReturnsSameValue(): void
    {
        $exception = new InvalidOperationCodeException(1, 2);
        self::assertSame(1, $exception->getIndex());
    }

    public function testGetOperationCode_ConstructedWithOperationCode_ReturnsSameValue(): void
    {
        $exception = new InvalidOperationCodeException(1, 2);
        self::assertSame(2, $exception->getOperationCode());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new InvalidOperationCodeException(1, 2);
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new InvalidOperationCodeException(1, 2);
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new InvalidOperationCodeException(1, 2, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
