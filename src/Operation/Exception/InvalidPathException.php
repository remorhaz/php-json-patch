<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use Throwable;
use UnexpectedValueException;

final class InvalidPathException extends UnexpectedValueException implements ExceptionInterface
{
    public function __construct(
        private readonly int $index,
        private readonly string $property,
        private readonly mixed $path,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message: "Operation #$this->index: JSON pointer in '$this->property' property must be a string",
            previous: $previous,
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
