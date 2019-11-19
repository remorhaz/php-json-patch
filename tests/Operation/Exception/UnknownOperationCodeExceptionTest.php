<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Patch\Operation\Exception\UnknownOperationCodeException;

/**
 * @covers \Remorhaz\JSON\Patch\Operation\Exception\UnknownOperationCodeException
 */
class UnknownOperationCodeExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new UnknownOperationCodeException(1, 'a');
        self::assertSame(
            'Operation #1: unknown operation code \'a\'',
            $exception->getMessage()
        );
    }

    public function testGetIndex_ConstructedWithIndex_ReturnsSameValue(): void
    {
        $exception = new UnknownOperationCodeException(1, 'a');
        self::assertSame(1, $exception->getIndex());
    }

    public function testGetOperationCode_ConstructedWithProperty_ReturnsSameValue(): void
    {
        $exception = new UnknownOperationCodeException(1, 'a');
        self::assertSame('a', $exception->getOperationCode());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new UnknownOperationCodeException(1, 'a');
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new UnknownOperationCodeException(1, 'a');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new UnknownOperationCodeException(1, 'a', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
