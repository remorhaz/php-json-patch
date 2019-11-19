<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use RangeException;
use Throwable;

final class UnknownOperationCodeException extends RangeException implements ExceptionInterface
{

    private $index;

    private $operationCode;

    public function __construct(int $index, string $operationCode, Throwable $previous = null)
    {
        $this->index = $index;
        $this->operationCode = $operationCode;
        parent::__construct(
            "Operation #{$this->index}: unknown operation code '{$this->operationCode}'",
            0,
            $previous
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
