<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Operation;

use Remorhaz\JSON\Data\Comparator\ComparatorInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Processor\ProcessorInterface as PointerProcessorInterface;
use Remorhaz\JSON\Pointer\Query\QueryFactoryInterface as PointerQueryFactoryInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface;

use function is_string;

final class OperationFactory implements OperationFactoryInterface
{
    private const OPERATION_ADD = 'add';

    private const OPERATION_REMOVE = 'remove';

    private const OPERATION_REPLACE = 'replace';

    private const OPERATION_TEST = 'test';

    private const OPERATION_COPY = 'copy';

    private const OPERATION_MOVE = 'move';

    private const POINTER_PATH = 'path';

    private const POINTER_FROM = 'from';

    public function __construct(
        private PointerQueryFactoryInterface $pointerQueryFactory,
        private PointerProcessorInterface $pointerProcessor,
        private ComparatorInterface $equalComparator,
    ) {
    }

    public function fromJson(NodeValueInterface $jsonValue, int $index): OperationInterface
    {
        $operationCode = $this->getOperationCode($jsonValue, $index);

        return match ($operationCode) {
            self::OPERATION_ADD => new AddOperation(
                $index,
                $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index),
                $this->extractValue($jsonValue, $index),
            ),
            self::OPERATION_REMOVE => new RemoveOperation(
                $index,
                $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index),
            ),
            self::OPERATION_REPLACE => new ReplaceOperation(
                $index,
                $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index),
                $this->extractValue($jsonValue, $index),
            ),
            self::OPERATION_TEST => new TestOperation(
                $index,
                $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index),
                $this->extractValue($jsonValue, $index),
                $this->equalComparator,
            ),
            self::OPERATION_COPY => new CopyOperation(
                $index,
                $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index),
                $this->extractPathPointer($jsonValue, self::POINTER_FROM, $index),
            ),
            self::OPERATION_MOVE => new MoveOperation(
                $index,
                $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index),
                $this->extractPathPointer($jsonValue, self::POINTER_FROM, $index),
            ),
            default => throw new Exception\UnknownOperationCodeException($index, $operationCode),
        };
    }

    private function getOperationCode(NodeValueInterface $jsonValue, int $index): string
    {
        $result = $this
            ->pointerProcessor
            ->select($this->pointerQueryFactory->createQuery('/op'), $jsonValue);
        if (!$result->exists()) {
            throw new Exception\OperationCodeNotFoundException($index);
        }
        $operationCode = $result->decode();

        return is_string($operationCode)
            ? $operationCode
            : throw new Exception\InvalidOperationCodeException($operationCode, $index);
    }

    private function extractPathPointer(NodeValueInterface $operation, string $property, int $index): QueryInterface
    {
        $result = $this
            ->pointerProcessor
            ->select($this->pointerQueryFactory->createQuery("/{$property}"), $operation);
        $path = $result->exists()
            ? $result->decode()
            : throw new Exception\PathNotFoundException($index, $property);

        return is_string($path)
            ? $this
                ->pointerQueryFactory
                ->createQuery($path)
            : throw new Exception\InvalidPathException($index, $property, $path);
    }

    private function extractValue(NodeValueInterface $operation, int $index): NodeValueInterface
    {
        $result = $this
            ->pointerProcessor
            ->select($this->pointerQueryFactory->createQuery('/value'), $operation);

        return $result->exists()
            ? $result->get()
            : throw new Exception\ValueNotFoundException($index);
    }
}
