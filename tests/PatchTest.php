<?php

namespace Remorhaz\JSON\Test\Patch;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoder;
use Remorhaz\JSON\Data\Export\ValueEncoder;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory as DecodedNodeValueFactory;
use Remorhaz\JSON\Patch\Patch;

class PatchTest extends TestCase
{


    /**
     * @param mixed $data
     * @param array $patchData
     * @param mixed $expectedData
     * @dataProvider providerValidSpecPatch_Result
     */
    public function testApply_ValidSpecPatch_Applied($data, array $patchData, $expectedData): void
    {
        $decodedNodeValueFactory = DecodedNodeValueFactory::create();
        $dataValue = $decodedNodeValueFactory->createValue($data);
        $patchDataValue = $decodedNodeValueFactory->createValue($patchData);
        $patch = new Patch($dataValue);
        $patch->apply($patchDataValue);
        $actualData = (new ValueDecoder)->exportValue($patch->getOutputData());
        $this->assertEquals($expectedData, $actualData);
    }


    public function providerValidSpecPatch_Result(): array
    {
        $dataSetList = [];
        foreach ($this->getSpecTests() as $testInfo) {
            if (isset($testInfo->error) || !isset($testInfo->expected)) {
                continue;
            }
            $dataSet = [
                $testInfo->doc,
                $testInfo->patch,
                $testInfo->expected,
            ];
            if (isset($testInfo->comment)) {
                $dataSetList[$testInfo->comment] = $dataSet;
            } else {
                $dataSetList[] = $dataSet;
            }
        }
        return $dataSetList;
    }


    /**
     * @param mixed $data
     * @param array $patchData
     * @dataProvider providerInvalidPatch
     * @expectedException \RuntimeException
     */
    public function testApply_InvalidSpecPatch_ExceptionThrown($data, array $patchData): void
    {
        $dataWriter = new Writer($data);
        $patchDataSelector = new Selector($patchData);
        (new Patch($dataWriter))->apply($patchDataSelector);
    }


    public function providerInvalidSpecPatch(): array
    {
        $dataSetList = [];
        foreach ($this->getSpecTests() as $testInfo) {
            if (!isset($testInfo->error)) {
                continue;
            }
            $dataSet = [
                $testInfo->doc,
                $testInfo->patch,
            ];
            if (isset($testInfo->comment)) {
                $dataSetList[$testInfo->comment] = $dataSet;
            } else {
                $dataSetList[] = $dataSet;
            }
        }
        return $dataSetList;
    }

    /**
     * @param mixed $data
     * @param array $patchData
     * @param mixed $expectedData
     * @dataProvider providerValidPatch_Result
     */
    public function testApply_ValidPatch_Applied($data, array $patchData, $expectedData): void
    {
        $dataWriter = new Writer($data);
        $patchDataSelector = new Selector($patchData);
        (new Patch($dataWriter))->apply($patchDataSelector);
        $this->assertEquals($expectedData, $data);
    }


    public function providerValidPatch_Result(): array
    {
        $dataSetList = [];
        foreach ($this->getTests() as $testInfo) {
            if (isset($testInfo->error) || !isset($testInfo->expected)) {
                continue;
            }
            $dataSet = [
                $testInfo->doc,
                $testInfo->patch,
                $testInfo->expected,
            ];
            if (isset($testInfo->comment)) {
                $dataSetList[$testInfo->comment] = $dataSet;
            } else {
                $dataSetList[] = $dataSet;
            }
        }
        return $dataSetList;
    }

    /**
     * @param mixed $data
     * @param array $patchData
     * @dataProvider providerInvalidPatch
     * @expectedException \RuntimeException
     */
    public function testApply_InvalidPatch_ExceptionThrown($data, array $patchData): void
    {
        $dataWriter = new Writer($data);
        $patchDataSelector = new Selector($patchData);
        (new Patch($dataWriter))->apply($patchDataSelector);
    }


    public function providerInvalidPatch(): array
    {
        $dataSetList = [];
        foreach ($this->getTests() as $testInfo) {
            if (!isset($testInfo->error)) {
                continue;
            }
            $dataSet = [
                $testInfo->doc,
                $testInfo->patch,
            ];
            if (isset($testInfo->comment)) {
                $dataSetList[$testInfo->comment] = $dataSet;
            } else {
                $dataSetList[] = $dataSet;
            }
        }
        return $dataSetList;
    }


    private function getSpecTests(): array
    {
        $testsFile = realpath(__DIR__ . "/data/spec_tests.json");
        return $this->getTestInfoList($testsFile);
    }


    private function getTests(): array
    {
        $testsFile = realpath(__DIR__ . "/data/tests.json");
        return $this->getTestInfoList($testsFile);
    }


    private function getTestInfoList($fileName): array
    {
        $testInfoListJSON = file_get_contents($fileName);
        $testInfoList = json_decode($testInfoListJSON);
        $isNotDisabledTest = function(\stdClass $testInfo) {
            return !(isset($testInfo->disabled) && $testInfo->disabled);
        };
        return array_filter($testInfoList, $isNotDisabledTest);
    }
}
