<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Query\Exception;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Throwable;
use UnexpectedValueException;

final class InvalidPatchException extends UnexpectedValueException implements ExceptionInterface
{
    public function __construct(
        private NodeValueInterface $patch,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Patch must be an array", 0, $previous);
    }

    public function getPatch(): NodeValueInterface
    {
        return $this->patch;
    }
}
