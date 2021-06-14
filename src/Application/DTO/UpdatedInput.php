<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\DTO;

use Webmozart\Assert\Assert;

class UpdatedInput
{
    protected array $updatedProperties = [];

    protected function setProperty(string $propertyName, mixed $value): static
    {
        Assert::propertyExists($this, $propertyName);

        $this->{$propertyName} = $value;
        $this->addUpdatedProperty($propertyName);

        return $this;
    }

    protected function addUpdatedProperty(string $propertyName): static
    {
        Assert::propertyExists($this, $propertyName);

        if (!in_array($propertyName, $this->updatedProperties, true)) {
            $this->updatedProperties[] = $propertyName;
        }

        return $this;
    }

    /**
     * @param string $namedPropertyEnum classname of named property enum which to use
     */
    public function getUpdatedProperties(string $namedPropertyEnum): array
    {
        return array_map(
            fn (string $propName) => new NamedValue(new $namedPropertyEnum($propName), $this->{$propName}),
            $this->updatedProperties,
        );
    }
}
