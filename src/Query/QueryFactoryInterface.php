<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface QueryFactoryInterface
{

    public function createQuery(NodeValueInterface $patch): QueryInterface;
}
