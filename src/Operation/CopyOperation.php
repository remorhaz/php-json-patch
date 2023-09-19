<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface as PointerQueryInterface;

final class CopyOperation implements OperationInterface
{
    public function __construct(
        private int $index,
        private PointerQueryInterface $pathPointer,
        private PointerQueryInterface $fromPointer,
    ) {
    }

    public function apply(NodeValueInterface $input, PointerProcessorInterface $pointerProcessor): NodeValueInterface
    {
        $selectResult = $pointerProcessor->select($this->fromPointer, $input);

        return $pointerProcessor
            ->add($this->pathPointer, $input, $selectResult->get())
            ->get();
    }
}
