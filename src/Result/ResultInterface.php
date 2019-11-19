<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Result;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface ResultInterface
{

    public function encode(): string;

    public function decode();

    public function get(): NodeValueInterface;
}
