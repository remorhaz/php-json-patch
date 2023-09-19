<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Processor\Exception;

use RuntimeException;
use Throwable;

final class PatchNotAppliedException extends RuntimeException implements ExceptionInterface
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("Failed to apply patch", 0, $previous);
    }
}
