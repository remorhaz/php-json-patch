<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use RangeException;
use Throwable;

final class UnknownOperationCodeException extends RangeException implements ExceptionInterface
{
    public function __construct(
        private readonly int $index,
        private readonly string $operationCode,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message: "Operation #$this->index: unknown operation code '$this->operationCode'",
            previous: $previous,
        );
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getOperationCode(): string
    {
        return $this->operationCode;
    }
}
