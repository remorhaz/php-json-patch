<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use Throwable;
use UnexpectedValueException;

final class InvalidPathException extends UnexpectedValueException implements ExceptionInterface
{
    public function __construct(
        private int $index,
        private string $property,
        private mixed $path,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            "Operation #$this->index: JSON pointer in '$this->property' property must be a string",
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

    public function getPath(): mixed
    {
        return $this->path;
    }
}
