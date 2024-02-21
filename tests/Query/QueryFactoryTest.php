<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Test\Query;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Exception\ExceptionInterface as PatchExceptionInterface;
use Remorhaz\JSON\Patch\Operation\OperationFactoryInterface;
use Remorhaz\JSON\Patch\Query\LazyQuery;
use Remorhaz\JSON\Patch\Query\QueryFactory;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;

#[CoversClass(QueryFactory::class)]
class QueryFactoryTest extends TestCase
{
    public function testCreate_Always_ReturnsQueryFactoryInstance(): void
    {
        self::assertInstanceOf(QueryFactory::class, QueryFactory::create());
    }

    public function testCreateQuery_Constructed_ReturnsLazyQueryInstance(): void
    {
        $factory = QueryFactory::create();
        $patch = $this->createMock(NodeValueInterface::class);
        self::assertInstanceOf(LazyQuery::class, $factory->createQuery($patch));
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testCreateQuery_ConstructedWithOperationFactory_ResultUsesSameInstanceOnInvoke(): void
    {
        $operationFactory = $this->createMock(OperationFactoryInterface::class);
        $factory = new QueryFactory(
            $operationFactory,
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
        );

        $patch = NodeValueFactory::create()->createValue('[{"op":"remove","path":"/0"}]');
        $query = $factory->createQuery($patch);
        $input = $this->createMock(NodeValueInterface::class);
        $pointerProcessor = $this->createMock(PointerProcessorInterface::class);
        $operationFactory
            ->expects(self::atLeastOnce())
            ->method('fromJson');
        $query($input, $pointerProcessor);
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testCreateQuery_ConstructedWithEncoder_ResultOfInvocationUsesSameInstanceOnEncode(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $factory = new QueryFactory(
            $this->createMock(OperationFactoryInterface::class),
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
        );

        $patch = NodeValueFactory::create()->createValue('[{"op":"remove","path":"/0"}]');
        $query = $factory->createQuery($patch);
        $result = $query(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(PointerProcessorInterface::class),
        );
        $encoder
            ->expects(self::once())
            ->method('exportValue');
        $result->encode();
    }

    /**
     * @throws PatchExceptionInterface
     */
    public function testCreateQuery_ConstructedWithDecoder_ResultOfInvocationUsesSameInstanceOnDecode(): void
    {
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $factory = new QueryFactory(
            $this->createMock(OperationFactoryInterface::class),
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
        );

        $patch = NodeValueFactory::create()->createValue('[{"op":"remove","path":"/0"}]');
        $query = $factory->createQuery($patch);
        $result = $query(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(PointerProcessorInterface::class),
        );
        $decoder
            ->expects(self::once())
            ->method('exportValue');
        $result->decode();
    }
}
