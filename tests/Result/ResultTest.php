<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Result\Result;

#[CoversClass(Result::class)]
class ResultTest extends TestCase
{
    public function testGet_ConstructedWithValue_ReturnsSameValue(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $result = new Result(
            $value,
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
        );
        self::assertSame($value, $result->get());
    }

    public function testEncode_ConstructedWithValue_PassesSameInstanceToEncoder(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $result = new Result(
            $value,
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
        );
        $encoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->encode();
    }

    public function testEncode_EncoderReturnsValue_ReturnsSameValue(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $result = new Result(
            $value,
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
        );
        $encoder
            ->method('exportValue')
            ->willReturn('a');
        self::assertSame('a', $result->encode());
    }

    public function testDecode_ConstructedWithValue_PassesSameInstanceToDecoder(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $result = new Result(
            $value,
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
        );
        $decoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->decode();
    }

    public function testDecode_DecoderReturnsValue_ReturnsSameValue(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $result = new Result(
            $value,
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
        );
        $decoder
            ->method('exportValue')
            ->willReturn(1);
        self::assertSame(1, $result->decode());
    }
}
