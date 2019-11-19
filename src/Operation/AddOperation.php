<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface as PointerQueryInterface;

final class AddOperation implements OperationInterface
{

    private $index;

    private $pathPointer;

    private $value;

    public function __construct(
        int $index,
        PointerQueryInterface $pathPointer,
        NodeValueInterface $value
    ) {
        $this->index = $index;
        $this->pathPointer = $pathPointer;
        $this->value = $value;
    }

    public function apply(NodeValueInterface $input, PointerProcessorInterface $pointerProcessor): NodeValueInterface
    {
        return $pointerProcessor
            ->add($this->pathPointer, $input, $this->value)
            ->get();
    }
}
