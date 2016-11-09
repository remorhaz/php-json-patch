<?php

namespace Remorhaz\JSON\Patch;

use Remorhaz\JSON\Data\SelectableReaderInterface;
use Remorhaz\JSON\Pointer\Pointer;

class Patch
{

    private $dataReader;

    private $patchReader;

    private $dataPointer;

    private $patchPointer;


    public function __construct(SelectableReaderInterface $dataReader)
    {
        $this->dataReader = $dataReader;
    }


    public function apply(SelectableReaderInterface $patchReader)
    {
        $this
            ->setPatchReader($patchReader)
            ->getPatchReader()
            ->selectRoot();
        if (!$this->getPatchReader()->isArraySelected()) {
            throw new \RuntimeException("Patch must be an array");
        }
        $operationCount = $this
            ->getPatchReader()
            ->getElementCount();
        for ($operationIndex = 0; $operationIndex < $operationCount; $operationIndex++) {
            $this->performOperation($operationIndex);
        }
        return $this;
    }


    protected function getDataReader(): SelectableReaderInterface
    {
        return $this->dataReader;
    }


    protected function performOperation(int $index)
    {
        $op = $this->getPatchPointer()->read("/{$index}/op")->getData();
        $path = $this->getPatchPointer()->read("/{$index}/path")->getData();
        switch ($op) {
            case 'add':
                $valueReader = $this->getPatchPointer()->read("/{$index}/value");
                $this->getDataPointer()->add($path, $valueReader);
                break;

            case 'remove':
                $this->getDataPointer()->remove($path);
                break;

            case 'replace':
                $valueReader = $this->getPatchPointer()->read("/{$index}/value");
                $this->getDataPointer()->replace($path, $valueReader);
                break;

            case 'test':
                $expectedValueReader = $this->getPatchPointer()->read("/{$index}/value");
                $actualValueReader = $this->getDataPointer()->read($path);
                if ($expectedValueReader->getData() !== $actualValueReader->getData()) {
                    throw new \RuntimeException("Test operation failed");
                }
                break;

            case 'copy':
                $from = $this->getPatchPointer()->read("/{$index}/from")->getData();
                $valueReader = $this->getDataPointer()->read($from);
                $this->getDataPointer()->add($path, $valueReader);
                break;

            case 'move':
                $from = $this->getPatchPointer()->read("/{$index}/from")->getData();
                $valueReader = $this->getDataPointer()->read($from);
                $this
                    ->getDataPointer()
                    ->remove($from)
                    ->add($path, $valueReader);
                break;

            default:
                throw new \RuntimeException("Unknown operation '{$op}'");
        }
        return $this;
    }


    protected function setPatchReader(SelectableReaderInterface $patchReader)
    {
        $this->patchReader = $patchReader;
        return $this;
    }


    protected function getPatchReader(): SelectableReaderInterface
    {
        if (null === $this->patchReader) {
            throw new \LogicException("Patch reader is not set");
        }
        return $this->patchReader;
    }


    protected function getPatchPointer(): Pointer
    {
        if (null === $this->patchPointer) {
            $this->patchPointer = new Pointer($this->getPatchReader());
        }
        return $this->patchPointer;
    }


    protected function getDataPointer(): Pointer
    {
        if (null === $this->dataPointer) {
            $this->dataPointer = new Pointer($this->getDataReader());
        }
        return $this->dataPointer;
    }
}
