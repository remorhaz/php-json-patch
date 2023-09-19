<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use RangeException;
use Throwable;

final class UnknownOperationCodeException extends RangeException implements ExceptionInterface
{
    public function __construct(
        private int $index,
        private string $operationCode,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            "Operation #$this->index: unknown operation code '$this->operationCode'",
            0,
            $previous,
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
