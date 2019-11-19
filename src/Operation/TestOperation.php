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

    private $index;

    private $pathPointer;

    private $value;

    private $equalComparator;

    public function __construct(
        int $index,
        PointerQueryInterface $pathPointer,
        NodeValueInterface $value,
        ComparatorInterface $equalComparator
    ) {
        $this->index = $index;
        $this->pathPointer = $pathPointer;
        $this->value = $value;
        $this->equalComparator = $equalComparator;
    }

    public function apply(NodeValueInterface $input, PointerProcessorInterface $pointerProcessor): NodeValueInterface
    {
        $selectResult = $pointerProcessor->select($this->pathPointer, $input);

        if ($this->matches($selectResult)) {
            return $input;
        }

        throw new Exception\TestFailedException($this->index, $input, $this->pathPointer->getSource(), $this->value);
    }

    private function matches(PointerResultInterface $selectResult): bool
    {
        if (!$selectResult->exists()) {
            return false;
        }

        return $this
            ->equalComparator
            ->compare($selectResult->get(), $this->value);
    }
}
