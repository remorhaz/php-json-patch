<?php

namespace Remorhaz\JSON\Test\Patch;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoder;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory as DecodedJsonNodeValueFactory;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory as EncodedJsonNodeValueFactory;
use Remorhaz\JSON\Patch\Processor\Exception\ExceptionInterface as ProcessorExceptionInterface;
use Remorhaz\JSON\Patch\Processor\Exception\PatchNotAppliedException;
use Remorhaz\JSON\Patch\Processor\Processor;
use Remorhaz\JSON\Patch\Query\QueryFactory;
use stdClass;

/**
 * @coversNothing
 */
class AcceptanceTest extends TestCase
{

    /**
     * @param mixed $data
     * @param array $patchData
     * @param mixed $expectedData
     * @dataProvider providerValidSpecPatch_Result
     * @throws ProcessorExceptionInterface
     */
    public function testApply_ValidSpecPatch_Applied($data, array $patchData, $expectedData): void
    {
        $decodedNodeValueFactory = DecodedJsonNodeValueFactory::create();
        $dataValue = $decodedNodeValueFactory->createValue($data);
        $patchDataValue = $decodedNodeValueFactory->createValue($patchData);
        $query = QueryFactory::create()->createQuery($patchDataValue);
        $actualData = Processor::create()
            ->apply($query, $dataValue)
            ->decode();
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
     * @throws ProcessorExceptionInterface
     */
    public function testApply_InvalidSpecPatch_ExceptionThrown($data, array $patchData): void
    {
        $decodedNodeValueFactory = DecodedJsonNodeValueFactory::create();
        $dataValue = $decodedNodeValueFactory->createValue($data);
        $patchDataValue = $decodedNodeValueFactory->createValue($patchData);
        $queryFactory = QueryFactory::create();
        $query = $queryFactory->createQuery($patchDataValue);
        $patch = Processor::create();
        $this->expectException(PatchNotAppliedException::class);
        $patch->apply($query, $dataValue);
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
     * @throws ProcessorExceptionInterface
     */
    public function testApply_ValidPatch_Applied($data, array $patchData, $expectedData): void
    {
        $decodedNodeValueFactory = DecodedJsonNodeValueFactory::create();
        $dataValue = $decodedNodeValueFactory->createValue($data);
        $patchDataValue = $decodedNodeValueFactory->createValue($patchData);
        $queryFactory = QueryFactory::create();
        $query = $queryFactory->createQuery($patchDataValue);
        $actualData = Processor::create()
            ->apply($query, $dataValue)
            ->decode();
        $this->assertEquals($expectedData, $actualData);
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
     * @throws ProcessorExceptionInterface
     */
    public function testApply_InvalidPatch_ExceptionThrown($data, array $patchData): void
    {
        $decodedNodeValueFactory = DecodedJsonNodeValueFactory::create();
        $dataValue = $decodedNodeValueFactory->createValue($data);
        $patchDataValue = $decodedNodeValueFactory->createValue($patchData);
        $queryFactory = QueryFactory::create();
        $query = $queryFactory->createQuery($patchDataValue);
        $patch = Processor::create();
        $this->expectException(PatchNotAppliedException::class);
        $patch->apply($query, $dataValue);
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
                $dataSetList[$testInfo->error] = $dataSet;
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
        $testInfoListValue = EncodedJsonNodeValueFactory::create()->createValue($testInfoListJSON);
        $testInfoList = (new ValueDecoder)->exportValue($testInfoListValue);
        $isNotDisabledTest = function (stdClass $testInfo) {
            return !(isset($testInfo->disabled) && $testInfo->disabled);
        };

        return array_filter($testInfoList, $isNotDisabledTest);
    }
}
