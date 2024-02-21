<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Query\Exception;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use RuntimeException;
use Throwable;

final class OperationNotLoadedException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        private readonly int $index,
        private readonly NodeValueInterface $patch,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Failed to load operation #$this->index from patch", previous: $previous);
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getPatch(): NodeValueInterface
    {
        return $this->patch;
    }
}
