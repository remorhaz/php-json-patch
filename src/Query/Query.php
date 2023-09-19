<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Query;

use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Operation\OperationInterface;
use Remorhaz\JSON\Patch\Result\Result;
use Remorhaz\JSON\Patch\Result\ResultInterface;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;

final class Query implements QueryInterface
{
    private $encoder;

    private $decoder;

    private $operations;

    public function __construct(
        ValueEncoderInterface $encoder,
        ValueDecoderInterface $decoder,
        OperationInterface ...$operations
    ) {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->operations = $operations;
    }

    public function __invoke(NodeValueInterface $data, PointerProcessorInterface $pointerProcessor): ResultInterface
    {
        $output = $data;
        foreach ($this->operations as $operation) {
            $output = $operation->apply($output, $pointerProcessor);
        }

        return new Result($output, $this->encoder, $this->decoder);
    }
}
