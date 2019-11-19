<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface as PointerQueryInterface;

final class MoveOperation implements OperationInterface
{

    private $index;

    private $pathPointer;

    private $fromPointer;

    public function __construct(
        int $index,
        PointerQueryInterface $pathPointer,
        PointerQueryInterface $fromPointer
    ) {
        $this->index = $index;
        $this->pathPointer = $pathPointer;
        $this->fromPointer = $fromPointer;
    }

    public function apply(NodeValueInterface $input, PointerProcessorInterface $pointerProcessor): NodeValueInterface
    {
        $selectResult = $pointerProcessor->select($this->fromPointer, $input);
        $deleteResult = $pointerProcessor->delete($this->fromPointer, $input);

        return $pointerProcessor
            ->add($this->pathPointer, $deleteResult->get(), $selectResult->get())
            ->get();
    }
}
