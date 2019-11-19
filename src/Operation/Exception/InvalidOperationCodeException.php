<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use Throwable;
use UnexpectedValueException;

final class InvalidOperationCodeException extends UnexpectedValueException implements ExceptionInterface
{

    private $index;

    private $operationCode;

    public function __construct(int $index, $operationCode, Throwable $previous = null)
    {
        $this->index = $index;
        $this->operationCode = $operationCode;
        parent::__construct(
            "Operation #{$this->index}: operation code in 'op' property must be a string",
            0,
            $previous
        );
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getOperationCode()
    {
        return $this->operationCode;
    }
}
