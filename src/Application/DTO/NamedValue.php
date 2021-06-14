<?php

namespace Nalgoo\Common\Application\DTO;

use Nalgoo\Common\Domain\Enums\StringEnum;

class NamedValue
{
    protected StringEnum $name;

    protected mixed $value;

    public function __construct(StringEnum $name, mixed $value)
    {
        $this->value = $value;
        $this->name = $name;
    }

    public function getName(): StringEnum
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
