<?php

namespace Nalgoo\Common\Domain;

interface IntValueInterface
{
    public static function fromInt(int $value) : static;

    public function toInt(): int;
}
