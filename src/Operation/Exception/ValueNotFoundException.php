<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use RuntimeException;
use Throwable;

final class ValueNotFoundException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        private readonly int $index,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message: "Operation #$this->index: value not found in 'value' property",
            previous: $previous,
        );
    }

    public function getIndex(): int
    {
        return $this->index;
    }
}
