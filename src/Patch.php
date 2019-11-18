<?php

namespace Remorhaz\JSON\Patch;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory as DecodedJsonNodeValueFactory;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Processor\Result\ResultInterface;
use Remorhaz\JSON\Pointer\Query\QueryFactory;
use Remorhaz\JSON\Pointer\Query\QueryInterface;
use RuntimeException;
use function is_string;

class Patch
{

    private $queryFactory;

    private $queryProcessor;

    private $decodedJsonNodeValueFactory;

    private $outputData;

    public function __construct(NodeValueInterface $inputData)
    {
        $this->outputData = $inputData;
        $this->queryFactory = QueryFactory::create();
        $this->queryProcessor = Processor::create();
        $this->decodedJsonNodeValueFactory = DecodedJsonNodeValueFactory::create();
    }

    public function apply(NodeValueInterface $patch)
    {
        if (!$patch instanceof ArrayValueInterface) {
            throw new RuntimeException("Patch must be an array");
        }
        foreach ($patch->createChildIterator() as $element) {
            $this->performOperation($element);
        }

        return $this;
    }

    protected function performOperation(NodeValueInterface $element)
    {
        $operation = $this
            ->queryProcessor
            ->select($this->queryFactory->createQuery('/op'), $element)
            ->decode();
        if (!is_string($operation)) {
            throw new RuntimeException("Invalid patch operation");
        }
        $pathQuery = $this->getOperationPath($element);
        switch ($operation) {
            case 'add':
                $result = $this
                    ->queryProcessor
                    ->add(
                        $pathQuery,
                        $this->outputData,
                        $this->getOperationValue($element)
                    );
                $this->outputData = $this->getResultValue($result);
                break;

            case 'remove':
                $result = $this
                    ->queryProcessor
                    ->delete($pathQuery, $this->outputData);
                $this->outputData = $this->getResultValue($result);
                break;

            case 'replace':
                $result = $this
                    ->queryProcessor
                    ->replace(
                        $pathQuery,
                        $this->outputData,
                        $this->getOperationValue($element)
                    );
                $this->outputData = $this->getResultValue($result);
                break;

            case 'test':
                $result = $this
                    ->queryProcessor
                    ->select($pathQuery, $this->outputData);
                if (!$result->exists()) {
                    throw new RuntimeException("Test operation failed");
                }
                break;

            case 'copy':
                $fromResult = $this
                    ->queryProcessor
                    ->select(
                        $this->getOperationFrom($element),
                        $this->outputData
                    );
                $result = $this
                    ->queryProcessor
                    ->add(
                        $pathQuery,
                        $this->outputData,
                        $this->getResultValue($fromResult)
                    );
                $this->outputData = $this->getResultValue($result);
                break;

            case 'move':
                $fromPathQuery = $this->getOperationFrom($element);
                $fromResult = $this
                    ->queryProcessor
                    ->select($fromPathQuery, $this->outputData);
                $removeResult = $this
                    ->queryProcessor
                    ->delete($fromPathQuery, $this->outputData);
                $result = $this
                    ->queryProcessor
                    ->add(
                        $pathQuery,
                        $this->getResultValue($removeResult),
                        $this->getResultValue($fromResult)
                    );
                $this->outputData = $this->getResultValue($result);
                break;

            default:
                throw new RuntimeException("Unknown operation '{$operation}'");
        }

        return $this;
    }

    private function getOperationPath(NodeValueInterface $operation): QueryInterface
    {
        $path = $this
            ->queryProcessor
            ->select($this->queryFactory->createQuery('/path'), $operation)
            ->decode();
        if (!is_string($path)) {
            throw new RuntimeException("Invalid operation path");
        }

        return $this
            ->queryFactory
            ->createQuery($path);
    }

    private function getOperationFrom(NodeValueInterface $operation): QueryInterface
    {
        $path = $this
            ->queryProcessor
            ->select($this->queryFactory->createQuery('/from'), $operation)
            ->decode();
        if (!is_string($path)) {
            throw new RuntimeException("Invalid operation from");
        }

        return $this
            ->queryFactory
            ->createQuery($path);
    }

    private function getOperationValue(NodeValueInterface $operation): NodeValueInterface
    {
        $result = $this
            ->queryProcessor
            ->select($this->queryFactory->createQuery('/value'), $operation);

        if (!$result->exists()) {
            throw new RuntimeException("Patch result not found");
        }

        return $this->getResultValue($result);
    }

    private function getResultValue(ResultInterface $result): NodeValueInterface
    {
        return $this
            ->decodedJsonNodeValueFactory
            ->createValue($result->decode());
    }

    public function getOutputData(): NodeValueInterface
    {
        return $this->outputData;
    }
}
