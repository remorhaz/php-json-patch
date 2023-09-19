<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Result;

use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class Result implements ResultInterface
{
    public function __construct(
        private NodeValueInterface $value,
        private ValueEncoderInterface $encoder,
        private ValueDecoderInterface $decoder,
    ) {
    }

    public function encode(): string
    {
        return $this
            ->encoder
            ->exportValue($this->value);
    }

    public function decode(): mixed
    {
        return $this
            ->decoder
            ->exportValue($this->value);
    }

    public function get(): NodeValueInterface
    {
        return $this->value;
    }
}
