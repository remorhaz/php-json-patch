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

    private $pointerQueryFactory;

    private $pointerProcessor;

    private $equalComparator;

    public function __construct(
        PointerQueryFactoryInterface $pointerQueryFactory,
        PointerProcessorInterface $pointerProcessor,
        ComparatorInterface $equalComparator
    ) {
        $this->pointerQueryFactory = $pointerQueryFactory;
        $this->pointerProcessor = $pointerProcessor;
        $this->equalComparator = $equalComparator;
    }

    public function fromJson(NodeValueInterface $jsonValue, int $index): OperationInterface
    {
        $operationCode = $this->getOperationCode($jsonValue, $index);
        switch ($operationCode) {
            case self::OPERATION_ADD:
                return new AddOperation(
                    $index,
                    $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index),
                    $this->extractValue($jsonValue, $index)
                );

            case self::OPERATION_REMOVE:
                return new RemoveOperation(
                    $index,
                    $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index)
                );

            case self::OPERATION_REPLACE:
                return new ReplaceOperation(
                    $index,
                    $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index),
                    $this->extractValue($jsonValue, $index)
                );

            case self::OPERATION_TEST:
                return new TestOperation(
                    $index,
                    $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index),
                    $this->extractValue($jsonValue, $index),
                    $this->equalComparator
                );

            case self::OPERATION_COPY:
                return new CopyOperation(
                    $index,
                    $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index),
                    $this->extractPathPointer($jsonValue, self::POINTER_FROM, $index)
                );

            case self::OPERATION_MOVE:
                return new MoveOperation(
                    $index,
                    $this->extractPathPointer($jsonValue, self::POINTER_PATH, $index),
                    $this->extractPathPointer($jsonValue, self::POINTER_FROM, $index)
                );
        }

        throw new Exception\UnknownOperationCodeException($index, $operationCode);
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
        if (is_string($operationCode)) {
            return $operationCode;
        }

        throw new Exception\InvalidOperationCodeException($operationCode, $index);
    }

    private function extractPathPointer(NodeValueInterface $operation, string $property, int $index): QueryInterface
    {
        $result = $this
            ->pointerProcessor
            ->select($this->pointerQueryFactory->createQuery("/{$property}"), $operation);
        if (!$result->exists()) {
            throw new Exception\PathNotFoundException($index, $property);
        }
        $path = $result->decode();
        if (!is_string($path)) {
            throw new Exception\InvalidPathException($index, $property, $path);
        }

        return $this
            ->pointerQueryFactory
            ->createQuery($path);
    }

    private function extractValue(NodeValueInterface $operation, int $index): NodeValueInterface
    {
        $result = $this
            ->pointerProcessor
            ->select($this->pointerQueryFactory->createQuery('/value'), $operation);

        if ($result->exists()) {
            return $result->get();
        }

        throw new Exception\ValueNotFoundException($index);
    }
}
