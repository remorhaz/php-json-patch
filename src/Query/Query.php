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

use function array_values;

final class Query implements QueryInterface
{
    /**
     * @var list<OperationInterface>
     */
    private readonly array $operations;

    public function __construct(
        private readonly ValueEncoderInterface $encoder,
        private readonly ValueDecoderInterface $decoder,
        OperationInterface ...$operations,
    ) {
        $this->operations = array_values($operations);
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
