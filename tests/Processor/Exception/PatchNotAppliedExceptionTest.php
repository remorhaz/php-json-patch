<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Processor\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Patch\Processor\Exception\PatchNotAppliedException;

/**
 * @covers \Remorhaz\JSON\Patch\Processor\Exception\PatchNotAppliedException
 */
class PatchNotAppliedExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new PatchNotAppliedException;
        self::assertSame('Failed to apply patch', $exception->getMessage());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new PatchNotAppliedException;
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new PatchNotAppliedException;
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new PatchNotAppliedException($previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
