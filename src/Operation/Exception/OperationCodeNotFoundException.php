<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use RuntimeException;
use Throwable;

final class OperationCodeNotFoundException extends RuntimeException implements ExceptionInterface
{

    private $index;

    public function __construct(int $index, Throwable $previous = null)
    {
        $this->index = $index;
        parent::__construct(
            "Operation #{$this->index}: operation code not found in property 'op'",
            0,
            $previous
        );
    }

    public function getIndex(): int
    {
        return $this->index;
    }
}
