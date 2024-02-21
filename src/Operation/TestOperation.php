<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation;

use Remorhaz\JSON\Data\Comparator\ComparatorInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Processor\Result\ResultInterface as PointerResultInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface as PointerQueryInterface;

final class TestOperation implements OperationInterface
{
    public function __construct(
        private readonly int $index,
        private readonly PointerQueryInterface $pathPointer,
        private readonly NodeValueInterface $value,
        private readonly ComparatorInterface $equalComparator,
    ) {
    }

    public function apply(NodeValueInterface $input, PointerProcessorInterface $pointerProcessor): NodeValueInterface
    {
        $selectResult = $pointerProcessor->select($this->pathPointer, $input);

        return $this->matches($selectResult)
            ? $input
            : throw new Exception\TestFailedException(
                $this->index,
                $input,
                $this->pathPointer->getSource(),
                $this->value,
            );
    }

    private function matches(PointerResultInterface $selectResult): bool
    {
        return $selectResult->exists() && $this
            ->equalComparator
            ->compare($selectResult->get(), $this->value);
    }
}
