<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Query;

use Collator;
use Remorhaz\JSON\Data\Comparator\EqualValueComparator;
use Remorhaz\JSON\Data\Export\ValueDecoder;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoder;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Operation\OperationFactory;
use Remorhaz\JSON\Patch\Operation\OperationFactoryInterface;
use Remorhaz\JSON\Pointer\Processor\Processor as PointerProcessor;
use Remorhaz\JSON\Pointer\Query\QueryFactory as PointerQueryFactory;

final class QueryFactory implements QueryFactoryInterface
{

    private $operationFactory;

    private $encoder;

    private $decoder;

    public static function create(): QueryFactoryInterface
    {
        $decoder = new ValueDecoder;

        return new self(
            new OperationFactory(
                PointerQueryFactory::create(),
                PointerProcessor::create(),
                new EqualValueComparator(new Collator('UTF-8'))
            ),
            new ValueEncoder($decoder),
            $decoder
        );
    }

    public function __construct(
        OperationFactoryInterface $operationFactory,
        ValueEncoderInterface $encoder,
        ValueDecoderInterface $decoder
    ) {
        $this->operationFactory = $operationFactory;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }

    public function createQuery(NodeValueInterface $patch): QueryInterface
    {
        return new LazyQuery($this->operationFactory, $this->encoder, $this->decoder, $patch);
    }
}
