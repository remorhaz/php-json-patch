<?php

namespace Remorhaz\JSON\Test\Patch;

use Remorhaz\JSON\Data\RawSelectableReader;
use Remorhaz\JSON\Data\RawSelectableWriter;
use Remorhaz\JSON\Patch\Patch;

class PatchTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param mixed $data
     * @param array $patchData
     * @param mixed $expectedData
     * @dataProvider providerValidPatch_Result
     */
    public function testApply_ValidPatch_Applied($data, array $patchData, $expectedData)
    {
        $dataWriter = new RawSelectableWriter($data);
        $patchDataReader = new RawSelectableReader($patchData);
        (new Patch($dataWriter))->apply($patchDataReader);
        $this->assertEquals($expectedData, $data);
    }


    public function providerValidPatch_Result(): array
    {
        return [
            'A.1' => [
                (object) ['foo' => 'bar'],
                [
                    (object) ['op' => 'add', 'path' => '/baz', 'value' => 'qux'],
                ],
                (object) ['baz' => 'qux', 'foo' => 'bar'],
            ],
            'A.2' => [
                (object) ['foo' => ['bar', 'baz']],
                [
                    (object) ['op' => 'add', 'path' => '/foo/1', 'value' => 'qux'],
                ],
                (object) ['foo' => ['bar', 'qux', 'baz']],
            ],
            'A.3' => [
                (object) ['baz' => 'qux', 'foo' => 'bar'],
                [
                    (object) ['op' => 'remove', 'path' => '/baz'],
                ],
                (object) ['foo' => 'bar'],
            ],
            'A.4' => [
                (object) ['foo' => ['bar', 'qux', 'baz']],
                [
                    (object) ['op' => 'remove', 'path' => '/foo/1'],
                ],
                (object) ['foo' => ['bar', 'baz']],
            ],
            'A.5' => [
                (object) ['baz' => 'qux', 'foo' => 'bar'],
                [
                    (object) ['op' => 'replace', 'path' => '/baz', 'value' => 'boo'],
                ],
                (object) ['baz' => 'boo', 'foo' => 'bar'],
            ],
            'A.6' => [
                (object) [
                    'foo' => (object) ['bar' => 'baz', 'waldo' => 'fred'],
                    'qux' => (object) ['corge' => 'grault'],
                ],
                [
                    (object) ['op' => 'move', 'from' => '/foo/waldo', 'path' => '/qux/thud'],
                ],
                (object) [
                    'foo' => (object) ['bar' => 'baz'],
                    'qux' => (object) ['corge' => 'grault', 'thud' => 'fred'],
                ],
            ],
            'A.7' => [
                (object) ['foo' => ['all', 'grass', 'cows', 'eat']],
                [
                    (object) ['op' => 'move', 'from' => '/foo/1', 'path' => '/foo/3'],
                ],
                (object) ['foo' => ['all', 'cows', 'eat', 'grass']],
            ],
            'A.10' => [
                (object) ['foo' => 'bar'],
                [
                    (object) ['op' => 'add', 'path' => '/child', 'value' => (object) ['grandchild' => (object) []]],
                ],
                (object) ['foo' => 'bar', 'child' => (object) ['grandchild' => (object) []]],
            ],
            'A.11' => [
                (object) ['foo' => 'bar'],
                [
                    (object) ['op' => 'add', 'path' => '/baz', 'value' => 'qux', 'xyz' => 123],
                ],
                (object) ['foo' => 'bar', 'baz' => 'qux'],
            ],
            'A.16' => [
                (object) ['foo' => ['bar']],
                [
                    (object) ['op' => 'add', 'path' => '/foo/-', 'value' => ['abc', 'def']],
                ],
                (object) ['foo' => ['bar', ['abc', 'def']]],
            ],
        ];
    }


    /**
     * @param $data
     * @param array $patchData
     * @dataProvider providerValidPatch_Success
     */
    public function testApply_ValidPatch_Success($data, array $patchData)
    {
        $dataWriter = new RawSelectableWriter($data);
        $patchDataReader = new RawSelectableReader($patchData);
        (new Patch($dataWriter))->apply($patchDataReader);
    }


    public function providerValidPatch_Success(): array
    {
        return [
            'A.8' => [
                (object) ['baz' => 'qux', 'foo' => ["a", 2, "c"]],
                [
                    (object) ['op' => 'test', 'path' => '/baz', 'value' => 'qux'],
                    (object) ['op' => 'test', 'path' => '/foo/1', 'value' => 2],
                ],
            ],
            'A.14' => [
                (object) ['/' => 9, '~1' => 10],
                [
                    (object) ['op' => 'test', 'path' => '/~01', 'value' => 10],
                ],
            ],
        ];
    }


    /**
     * @param $data
     * @param array $patchData
     * @dataProvider providerInvalidPatch
     * @expectedException \Exception
     */
    public function testApply_InvalidPatch_ExceptionThrown($data, array $patchData)
    {
        $dataWriter = new RawSelectableWriter($data);
        $patchDataReader = new RawSelectableReader($patchData);
        (new Patch($dataWriter))->apply($patchDataReader);
    }


    public function providerInvalidPatch(): array
    {
        return [
            'A.9' => [
                (object) ['baz' => 'qux'],
                [
                    (object) ['op' => 'test', 'path' => '/baz', 'value' => 'bar'],
                ],
            ],
            'A.12' => [
                (object) ['foo' => 'bar'],
                [
                    (object) ['op' => 'add', 'path' => '/baz/bat', 'value' => 'qux'],
                ],
            ],
            // A.13 cannot be tested.
            'A.14' => [
                (object) ['/' => 9, '~1' => 10],
                [
                    (object) ['op' => 'test', 'path' => '/~01', 'value' => "10"],
                ],
            ],
        ];
    }
}
