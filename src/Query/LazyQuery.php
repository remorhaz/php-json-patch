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

    private $operationFactory;

    private $encoder;

    private $decoder;

    private $patch;

    private $loadedQuery;

    public function __construct(
        OperationFactoryInterface $operationFactory,
        ValueEncoderInterface $encoder,
        ValueDecoderInterface $decoder,
        NodeValueInterface $patch
    ) {
        $this->operationFactory = $operationFactory;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->patch = $patch;
    }

    public function __invoke(NodeValueInterface $data, PointerProcessorInterface $pointerProcessor): ResultInterface
    {
        return $this->getLoadedQuery()($data, $pointerProcessor);
    }

    private function getLoadedQuery(): QueryInterface
    {
        if (!isset($this->loadedQuery)) {
            $this->loadedQuery = $this->loadQuery();
        }

        return $this->loadedQuery;
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
        if ($patch instanceof ArrayValueInterface) {
            return $patch->createChildIterator();
        }

        throw new Exception\InvalidPatchException($patch);
    }
}
