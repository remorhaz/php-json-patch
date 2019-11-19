<?php

namespace Remorhaz\JSON\Patch\Processor;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Query\QueryInterface;
use Remorhaz\JSON\Patch\Result\ResultInterface;
use Remorhaz\JSON\Pointer\Processor\Processor as PointerProcessor;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Throwable;

class Processor implements ProcessorInterface
{

    private $pointerProcessor;

    public static function create(): ProcessorInterface
    {
        return new self(PointerProcessor::create());
    }

    public function __construct(PointerProcessorInterface $pointerProcessor)
    {
        $this->pointerProcessor = $pointerProcessor;
    }

    public function apply(QueryInterface $query, NodeValueInterface $data): ResultInterface
    {
        try {
            return $query($data, $this->pointerProcessor);
        } catch (Throwable $e) {
            throw new Exception\PatchNotAppliedException($e);
        }
    }
}
