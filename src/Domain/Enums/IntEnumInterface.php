<?php

namespace Nalgoo\Common\Domain\Enums;

interface IntEnumInterface
{
    public static function fromInt(int $value) : static;

    public function asInt(): int;
}
