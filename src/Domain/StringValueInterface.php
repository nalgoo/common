<?php

namespace Nalgoo\Common\Domain;

interface StringValueInterface
{
    public static function fromString(string $value): static;

    public function toString(): string;
}
