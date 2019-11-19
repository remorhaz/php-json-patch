<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation\Exception;

use Throwable;
use UnexpectedValueException;

final class InvalidPathException extends UnexpectedValueException implements ExceptionInterface
{

    private $index;

    private $property;

    private $path;

    public function __construct(int $index, string $property, $path, Throwable $previous = null)
    {
        $this->index = $index;
        $this->property = $property;
        $this->path = $path;
        parent::__construct(
            "Operation #{$this->index}: JSON pointer in '{$this->property}' property must be a string",
            0,
            $previous
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

    public function getPath()
    {
        return $this->path;
    }
}
