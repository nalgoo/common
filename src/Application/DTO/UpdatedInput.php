<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\DTO;

use Webmozart\Assert\Assert;

class UpdatedInput
{
    protected array $updatedProperties = [];

    protected function addUpdatedProperty(string $propertyName): void
    {
        Assert::propertyExists($this, $propertyName);

        if (!in_array($propertyName, $this->updatedProperties, true)) {
            $this->updatedProperties[] = $propertyName;
        }
    }

    /**
     * @param string $namedPropertyEnum classname of named property enum which to use
     * @return array
     */
    public function getUpdatedUserProperties(string $namedPropertyEnum): array
    {
        return array_map(
            fn (string $propName) => new $namedPropertyEnum($propName, $this->{$propName}),
            $this->updatedProperties,
        );
    }
}
