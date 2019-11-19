<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Patch\Result;

use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class Result implements ResultInterface
{

    private $value;

    private $encoder;

    private $decoder;

    public function __construct(
        NodeValueInterface $value,
        ValueEncoderInterface $encoder,
        ValueDecoderInterface $decoder
    ) {
        $this->value = $value;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }

    public function encode(): string
    {
        return $this
            ->encoder
            ->exportValue($this->value);
    }

    public function decode()
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
