<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Processor;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Patch\Query\QueryInterface;
use Remorhaz\JSON\Patch\Result\ResultInterface;

interface ProcessorInterface
{

    /**
     * @param QueryInterface     $query
     * @param NodeValueInterface $data
     * @return ResultInterface
     * @throws Exception\ExceptionInterface
     */
    public function apply(QueryInterface $query, NodeValueInterface $data): ResultInterface;
}
