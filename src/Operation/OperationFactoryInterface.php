<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface OperationFactoryInterface
{
    public function fromJson(NodeValueInterface $jsonValue, int $index): OperationInterface;
}
