<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Query\Exception\PatchNotLoadedException;

/**
 * @covers \Remorhaz\JSON\Patch\Query\Exception\PatchNotLoadedException
 */
class PatchNotLoadedExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new PatchNotLoadedException(
            $this->createMock(NodeValueInterface::class)
        );
        self::assertSame('Failed to load patch', $exception->getMessage());
    }

    public function testGetPatch_ConstructedWithPatch_ReturnsSameInstance(): void
    {
        $patch = $this->createMock(NodeValueInterface::class);
        $exception = new PatchNotLoadedException($patch);
        self::assertSame($patch, $exception->getPatch());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new PatchNotLoadedException(
            $this->createMock(NodeValueInterface::class)
        );
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new PatchNotLoadedException(
            $this->createMock(NodeValueInterface::class)
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new PatchNotLoadedException(
            $this->createMock(NodeValueInterface::class),
            $previous
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
