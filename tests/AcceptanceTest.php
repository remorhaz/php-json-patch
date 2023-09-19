<?php

namespace Remorhaz\JSON\Patch\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoder;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory as DecodedJsonNodeValueFactory;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory as EncodedJsonNodeValueFactory;
use Remorhaz\JSON\Patch\Processor\Exception\ExceptionInterface as ProcessorExceptionInterface;
use Remorhaz\JSON\Patch\Processor\Exception\PatchNotAppliedException;
use Remorhaz\JSON\Patch\Processor\Processor;
use Remorhaz\JSON\Patch\Query\QueryFactory;

use function array_filter;
use function array_values;

/**
 * @coversNothing
 */
class AcceptanceTest extends TestCase
{
    /**
     * @param mixed        $data
     * @param list<object> $patchData
     * @param mixed        $expectedData
     * @dataProvider providerValidSpecPatch
     * @throws ProcessorExceptionInterface
     */
    public function testApply_ValidSpecPatch_Applied(mixed $data, array $patchData, mixed $expectedData): void
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

    /**
     * @return iterable<int|string, array{
     *      mixed,
     *      list<object{
     *          op:string,
     *          path:string,
     *          value:mixed,
     *      }>,
     *      mixed,
     *  }>
     */
    public static function providerValidSpecPatch(): iterable
    {
        $dataSetList = [];
        foreach (self::getSpecTests() as $testInfo) {
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
     * @param mixed        $data
     * @param list<object> $patchData
     * @dataProvider providerInvalidPatch
     * @throws ProcessorExceptionInterface
     */
    public function testApply_InvalidSpecPatch_ExceptionThrown(mixed $data, array $patchData): void
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

    /**
     * @return iterable<int|string, array{
     *      mixed,
     *      list<object{
     *          op:string,
     *          path:string,
     *          value:mixed,
     *      }>,
     *  }>
     */
    public static function providerInvalidSpecPatch(): iterable
    {
        $dataSetList = [];
        foreach (self::getSpecTests() as $testInfo) {
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
     * @param mixed        $data
     * @param list<object> $patchData
     * @param mixed        $expectedData
     * @dataProvider providerValidPatch
     * @throws ProcessorExceptionInterface
     */
    public function testApply_ValidPatch_Applied(mixed $data, array $patchData, mixed $expectedData): void
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

    /**
     * @return iterable<int|string, array{
     *      mixed,
     *      list<object{
     *          op:string,
     *          path:string,
     *          value:mixed,
     *      }>,
     *      mixed,
     *  }>
     */
    public static function providerValidPatch(): iterable
    {
        $dataSetList = [];
        foreach (self::getTests() as $testInfo) {
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
     * @param mixed        $data
     * @param list<object> $patchData
     * @dataProvider providerInvalidPatch
     * @throws ProcessorExceptionInterface
     */
    public function testApply_InvalidPatch_ExceptionThrown(mixed $data, array $patchData): void
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

    /**
     * @return iterable<string, array{
     *     mixed,
     *     list<object{
     *         op:string,
     *         path:string,
     *         value:mixed,
     *     }>,
     * }>
     */
    public static function providerInvalidPatch(): iterable
    {
        $dataSetList = [];
        foreach (self::getTests() as $testInfo) {
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

    /**
     * @return list<object{
     *     comment?:string,
     *     doc:mixed,
     *     patch:list<object{
     *         op:string,
     *         path:string,
     *         value:mixed,
     *     }>,
     *     expected?:mixed,
     *     disabled?:bool,
     *     error?:string,
     * }>
     */
    private static function getSpecTests(): array
    {
        $testsFile = realpath(__DIR__ . "/data/spec_tests.json");

        return self::getTestInfoList($testsFile);
    }

    /**
     * @return list<object{
     *     comment?:string,
     *     doc:mixed,
     *     patch:list<object{
     *         op:string,
     *         path:string,
     *         value:mixed,
     *     }>,
     *     expected?:mixed,
     *     disabled?:bool,
     *     error?:string,
     * }>
     */
    private static function getTests(): array
    {
        $testsFile = realpath(__DIR__ . "/data/tests.json");

        return self::getTestInfoList($testsFile);
    }

    /**
     * @param string $fileName
     * @return list<object{
     *     comment?:string,
     *     doc:mixed,
     *     patch:list<object{
     *         op:string,
     *         path:string,
     *         value:mixed,
     *     }>,
     *     expected:mixed,
     *     disabled?:bool,
     *     error?:string,
     * }>
     */
    private static function getTestInfoList(string $fileName): array
    {
        $testInfoListJSON = file_get_contents($fileName);
        $testInfoListValue = EncodedJsonNodeValueFactory::create()->createValue($testInfoListJSON);
        /**
         * @var list<object{
         *     comment?:string,
         *     doc:mixed,
         *     patch:list<object{
         *         op:string,
         *         path:string,
         *         value:mixed,
         *     }>,
         *     expected?:mixed,
         *     disabled?:bool,
         *     error?:string,
         * }> $testInfoList
         */
        $testInfoList = (array) (new ValueDecoder())->exportValue($testInfoListValue);

        return array_values(
            array_filter(
                $testInfoList,
                /**
                 * @param object{
                 *     comment?:string,
                 *     doc:mixed,
                 *     patch:list<object{
                 *         op:string,
                 *         path:string,
                 *         value:mixed,
                 *     }>,
                 *     expected?:mixed,
                 *     disabled?:bool,
                 *     error?:string,
                 *   } $testInfo
                 */
                fn (object $testInfo): bool => !($testInfo->disabled ?? false),
            ),
        );
    }
}
