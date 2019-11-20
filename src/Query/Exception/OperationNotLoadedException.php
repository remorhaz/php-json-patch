<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Query\Exception;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use RuntimeException;
use Throwable;

final class OperationNotLoadedException extends RuntimeException implements ExceptionInterface
{

    private $index;

    private $patch;

    public function __construct(int $index, NodeValueInterface $patch, Throwable $previous = null)
    {
        $this->index = $index;
        $this->patch = $patch;
        parent::__construct("Failed to load operation #{$this->index} from patch", 0, $previous);
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getPatch(): NodeValueInterface
    {
        return $this->patch;
    }
}
