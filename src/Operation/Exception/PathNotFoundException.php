<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use RuntimeException;
use Throwable;

final class PathNotFoundException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        private int $index,
        private string $property,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            "Operation #$this->index: JSON Pointer not found in '$this->property' property",
            0,
            $previous,
        );
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
