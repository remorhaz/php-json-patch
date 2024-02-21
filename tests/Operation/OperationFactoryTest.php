<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Operation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Comparator\ComparatorInterface;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Operation\AddOperation;
use Remorhaz\JSON\Patch\Operation\CopyOperation;
use Remorhaz\JSON\Patch\Operation\Exception\InvalidOperationCodeException;
use Remorhaz\JSON\Patch\Operation\Exception\InvalidPathException;
use Remorhaz\JSON\Patch\Operation\Exception\OperationCodeNotFoundException;
use Remorhaz\JSON\Patch\Operation\Exception\PathNotFoundException;
use Remorhaz\JSON\Patch\Operation\Exception\UnknownOperationCodeException;
use Remorhaz\JSON\Patch\Operation\Exception\ValueNotFoundException;
use Remorhaz\JSON\Patch\Operation\MoveOperation;
use Remorhaz\JSON\Patch\Operation\OperationFactory;
use Remorhaz\JSON\Patch\Operation\RemoveOperation;
use Remorhaz\JSON\Patch\Operation\ReplaceOperation;
use Remorhaz\JSON\Patch\Operation\TestOperation;
use Remorhaz\JSON\Pointer\Processor\Processor as PointerProcessor;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Processor\Result\ExistingResult;
use Remorhaz\JSON\Pointer\Query\QueryFactory as PointerQueryFactory;

#[CoversClass(OperationFactory::class)]
class OperationFactoryTest extends TestCase
{
    public function testFromJson_NoOpProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{}');
        $this->expectException(OperationCodeNotFoundException::class);
        $this->expectExceptionMessage('Operation #1:');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_NonStringOpProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":1}');
        $this->expectException(InvalidOperationCodeException::class);
        $this->expectExceptionMessage('Operation #1:');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_UnknownOperationInOpProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"a"}');
        $this->expectException(UnknownOperationCodeException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'a\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_AddOperationNoPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"add","value":[]}');
        $this->expectException(PathNotFoundException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_AddOperationNonStringPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"add","path":1,"value":[]}');
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_AddOperationNoValueProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"add","path":"/0"}');
        $this->expectException(ValueNotFoundException::class);
        $this->expectExceptionMessage('Operation #1');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_AddOperation_ReturnsAddOperationInstance(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"add","path":"/0","value":[]}');
        $operation = $factory->fromJson($jsonValue, 1);
        self::assertInstanceOf(AddOperation::class, $operation);
    }

    public function testFromJson_RemoveOperationNoPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"remove"}');
        $this->expectException(PathNotFoundException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_RemoveOperationNonStringPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"remove","path":1}');
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_RemoveOperation_ReturnsRemoveOperationInstance(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"remove","path":"/0"}');
        $operation = $factory->fromJson($jsonValue, 1);
        self::assertInstanceOf(RemoveOperation::class, $operation);
    }

    public function testFromJson_ReplaceOperationNoPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"replace","value":[]}');
        $this->expectException(PathNotFoundException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_ReplaceOperationNonStringPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"replace","path":1,"value":[]}');
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_ReplaceOperationNoValueProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"replace","path":"/0"}');
        $this->expectException(ValueNotFoundException::class);
        $this->expectExceptionMessage('Operation #1');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_ReplaceOperation_ReturnsAddOperationInstance(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"replace","path":"/0","value":[]}');
        $operation = $factory->fromJson($jsonValue, 1);
        self::assertInstanceOf(ReplaceOperation::class, $operation);
    }

    public function testFromJson_TestOperationNoPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"test","value":[]}');
        $this->expectException(PathNotFoundException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_TestOperationNonStringPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"test","path":1,"value":[]}');
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_TestOperationNoValueProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"test","path":"/0"}');
        $this->expectException(ValueNotFoundException::class);
        $this->expectExceptionMessage('Operation #1');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_TestOperation_ReturnsAddOperationInstance(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"test","path":"/0","value":[]}');
        $operation = $factory->fromJson($jsonValue, 1);
        self::assertInstanceOf(TestOperation::class, $operation);
    }

    public function testFromJson_TestOperationConstructedWithComparator_ResultUsesSameInstance(): void
    {
        $comparator = $this->createMock(ComparatorInterface::class);
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $comparator,
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"test","path":"/0","value":1}');
        $operation = $factory->fromJson($jsonValue, 1);
        $result = new ExistingResult(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(NodeValueInterface::class),
        );
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);
        $pointerProcessor
            ->method('select')
            ->willReturn($result);
        $value = $this->createMock(NodeValueInterface::class);
        $comparator
            ->expects(self::once())
            ->method('compare')
            ->willReturn(true);
        $operation->apply($value, $pointerProcessor);
    }

    public function testFromJson_CopyOperationNoPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"copy","from":"/0"}');
        $this->expectException(PathNotFoundException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_CopyOperationNonStringPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"copy","path":1,"from":"/0"}');
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_CopyOperationNoFromProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"copy","path":"/0"}');
        $this->expectException(PathNotFoundException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'from\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_CopyOperationNonStringFromProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"copy","path":"/0","from":1}');
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'from\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_CopyOperation_ReturnsCopyOperationInstance(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"copy","path":"/0","from":"/1"}');
        $operation = $factory->fromJson($jsonValue, 1);
        self::assertInstanceOf(CopyOperation::class, $operation);
    }

    public function testFromJson_MoveOperationNoPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"move","from":"/0"}');
        $this->expectException(PathNotFoundException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_MoveOperationNonStringPathProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"move","path":1,"from":"/0"}');
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'path\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_MoveOperationNoFromProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"move","path":"/0"}');
        $this->expectException(PathNotFoundException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'from\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_MoveOperationNonStringFromProperty_ThrowsException(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"move","path":"/0","from":1}');
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessageMatches('/Operation #1:.*\'from\'/');
        $factory->fromJson($jsonValue, 1);
    }

    public function testFromJson_MoveOperation_ReturnsCopyOperationInstance(): void
    {
        $factory = new OperationFactory(
            PointerQueryFactory::create(),
            PointerProcessor::create(),
            $this->createMock(ComparatorInterface::class),
        );
        $jsonValue = NodeValueFactory::create()->createValue('{"op":"move","path":"/0","from":"/1"}');
        $operation = $factory->fromJson($jsonValue, 1);
        self::assertInstanceOf(MoveOperation::class, $operation);
    }
}
