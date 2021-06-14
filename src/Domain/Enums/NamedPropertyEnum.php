<?php

namespace Nalgoo\Common\Domain\Enums;

use ReflectionClassConstant;
use Webmozart\Assert\Assert;

abstract class NamedPropertyEnum
{
    protected string $name;

    protected mixed $value;

    public function __construct(string $name, $value)
    {
        Assert::oneOf($value, static::getConstants());

        $this->value = $value;
        $this->name = $name;
    }

    protected static function getConstants(): array
    {
        $reflection = new \ReflectionClass(static::class);

        return array_values($reflection->getConstants(ReflectionClassConstant::IS_PUBLIC));
    }
}
