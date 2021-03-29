<?php

namespace Nalgoo\Common\Domain\Enums;

interface StringEnumInterface
{
    public static function fromString(string $value): static;

    public function asString(): string;
}
