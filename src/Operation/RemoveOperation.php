<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface as PointerQueryInterface;

final class RemoveOperation implements OperationInterface
{
    public function __construct(
        private readonly int $index,
        private readonly PointerQueryInterface $pathPointer,
    ) {
    }

    public function apply(NodeValueInterface $input, PointerProcessorInterface $pointerProcessor): NodeValueInterface
    {
        return $pointerProcessor
            ->delete($this->pathPointer, $input)
            ->get();
    }
}
