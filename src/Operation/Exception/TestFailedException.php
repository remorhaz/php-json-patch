<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use RuntimeException;
use Throwable;

final class TestFailedException extends RuntimeException implements ExceptionInterface
{

    private $index;

    private $document;

    private $path;

    private $expectedValue;

    public function __construct(
        int $index,
        NodeValueInterface $document,
        string $path,
        NodeValueInterface $expectedValue,
        Throwable $previous = null
    ) {
        $this->index = $index;
        $this->document = $document;
        $this->path = $path;
        $this->expectedValue = $expectedValue;
        parent::__construct(
            "Operation #{$this->index}: test operation failed at '{$path}'",
            0,
            $previous
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
