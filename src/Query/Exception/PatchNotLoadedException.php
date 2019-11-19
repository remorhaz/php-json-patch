<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Query\Exception;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use RuntimeException;
use Throwable;

final class PatchNotLoadedException extends RuntimeException implements ExceptionInterface
{

    private $patch;

    public function __construct(NodeValueInterface $patch, Throwable $previous = null)
    {
        $this->patch = $patch;
        parent::__construct("Failed to load patch", 0, $previous);
    }

    public function getPatch(): NodeValueInterface
    {
        return $this->patch;
    }
}
