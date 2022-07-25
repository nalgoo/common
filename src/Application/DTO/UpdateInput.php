<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\DTO;

use Webmozart\Assert\Assert;

class UpdateInput
{
	protected array $updatedProperties = [];

	protected function setProperty(\StringBackedEnum $property, mixed $value): static
	{
		Assert::propertyExists($this, $property->value);

		$this->{$property->value} = $value;
		$this->addUpdatedProperty($property);

		return $this;
	}

	protected function addUpdatedProperty(\StringBackedEnum $property): static
	{
		Assert::propertyExists($this, $property->value);

		if (!in_array($property, $this->updatedProperties, true)) {
			$this->updatedProperties[] = $property;
		}

		return $this;
	}

	public function getUpdatedProperties(): array
	{
		return array_map(
			fn(\StringBackedEnum $property) => new NamedValue($property, $this->{$property->value}),
			$this->updatedProperties,
		);
	}
}
