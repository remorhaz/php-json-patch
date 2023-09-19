<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;

interface OperationInterface
{
    public function apply(NodeValueInterface $input, PointerProcessorInterface $pointerProcessor): NodeValueInterface;
}
