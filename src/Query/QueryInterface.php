<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Exception\ExceptionInterface as PatchExceptionInterface;
use Remorhaz\JSON\Patch\Result\ResultInterface;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;

interface QueryInterface
{
    /**
     * @throws PatchExceptionInterface
     */
    public function __invoke(NodeValueInterface $data, PointerProcessorInterface $pointerProcessor): ResultInterface;
}
