<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use RuntimeException;
use Throwable;

final class TestFailedException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        private readonly int $index,
        private readonly NodeValueInterface $document,
        private readonly string $path,
        private readonly NodeValueInterface $expectedValue,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message: "Operation #$this->index: test operation failed at '$path'",
            previous: $previous,
        );
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getDocument(): NodeValueInterface
    {
        return $this->document;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getExpectedValue(): NodeValueInterface
    {
        return $this->expectedValue;
    }
}
