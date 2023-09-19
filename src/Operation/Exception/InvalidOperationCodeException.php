<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use Throwable;
use UnexpectedValueException;

final class InvalidOperationCodeException extends UnexpectedValueException implements ExceptionInterface
{
    public function __construct(
        private int $index,
        private mixed $operationCode,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            "Operation #$this->index: operation code in 'op' property must be a string",
            0,
            $previous,
        );
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getOperationCode(): mixed
    {
        return $this->operationCode;
    }
}
