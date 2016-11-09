<?php

namespace Remorhaz\JSON\Patch;

use Remorhaz\JSON\Data\SelectableReaderInterface;
use Remorhaz\JSON\Pointer\Pointer;

class Patch
{

    private $reader;


    public function __construct(SelectableReaderInterface $reader)
    {
        $this->reader = $reader;
    }


    public function apply(SelectableReaderInterface $patchReader)
    {
        $patchReader->selectRoot();
        if (!$patchReader->isArraySelected()) {
            throw new \Exception("Patch must be an array");
        }
        $operationCount = $patchReader->getElementCount();
        for ($operationIndex = 0; $operationIndex < $operationCount; $operationIndex++) {
            $this->performOperation($operationIndex, $patchReader);
        }
        return $this;
    }


    protected function getReader(): SelectableReaderInterface
    {
        return $this->reader;
    }


    protected function performOperation(int $index, SelectableReaderInterface $patchReader)
    {
        $patchPointer = new Pointer($patchReader);
        $op = $patchPointer->read("/{$index}/op")->getData();
        $path = $patchPointer->read("/{$index}/path")->getData();
        $dataPointer = new Pointer($this->getReader());
        switch ($op) {
            case 'add':
                $valueReader = $patchPointer->read("/{$index}/value");
                $dataPointer->add($path, $valueReader);
                break;

            case 'remove':
                $dataPointer->remove($path);
                break;

            case 'replace':
                $valueReader = $patchPointer->read("/{$index}/value");
                $dataPointer->replace($path, $valueReader);
                break;

            case 'test':
                $expectedValueReader = $patchPointer->read("/{$index}/value");
                $actualValueReader = $dataPointer->read($path);
                if ($expectedValueReader->getData() !== $actualValueReader->getData()) {
                    throw new \Exception("Test operation failed");
                }
                break;

            default:
                throw new \Exception("Unknown operation '{$op}'");
        }
        return $this;
    }
}
