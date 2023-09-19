<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Query;

use Iterator;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Operation\OperationFactoryInterface;
use Remorhaz\JSON\Patch\Result\ResultInterface;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Throwable;

final class LazyQuery implements QueryInterface
{
    private ?QueryInterface $loadedQuery = null;

    public function __construct(
        private OperationFactoryInterface $operationFactory,
        private ValueEncoderInterface $encoder,
        private ValueDecoderInterface $decoder,
        private NodeValueInterface $patch,
    ) {
    }

    public function __invoke(NodeValueInterface $data, PointerProcessorInterface $pointerProcessor): ResultInterface
    {
        return $this->getLoadedQuery()($data, $pointerProcessor);
    }

    private function getLoadedQuery(): QueryInterface
    {
        return $this->loadedQuery ??= $this->loadQuery();
    }

    private function loadQuery(): QueryInterface
    {
        $operations = [];
        foreach ($this->createOperationDataIterator($this->patch) as $index => $operationData) {
            try {
                $operations[] = $this
                    ->operationFactory
                    ->fromJson($operationData, $index);
            } catch (Throwable $e) {
                throw new Exception\OperationNotLoadedException($index, $this->patch, $e);
            }
        }

        return new Query($this->encoder, $this->decoder, ...$operations);
    }

    private function createOperationDataIterator(NodeValueInterface $patch): Iterator
    {
        return $patch instanceof ArrayValueInterface
            ? $patch->createChildIterator()
            : throw new Exception\InvalidPatchException($patch);
    }
}
