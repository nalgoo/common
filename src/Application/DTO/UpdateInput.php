<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\DTO;

use Webmozart\Assert\Assert;

class UpdateInput
{
	protected array $updatedProperties = [];

	protected function setProperty(\BackedEnum $property, mixed $value): static
	{
		Assert::propertyExists($this, $property->value);

		$this->{$property->value} = $value;
		$this->addUpdatedProperty($property);

		return $this;
	}

	protected function addUpdatedProperty(\BackedEnum $property): static
	{
		Assert::propertyExists($this, $property->value);

		if (!in_array($property, $this->updatedProperties, true)) {
			$this->updatedProperties[] = $property;
		}

		return $this;
	}

	/**
	 * @return NamedValue[]
	 */
	public function getUpdatedProperties(): array
	{
		return array_map(
			fn(\BackedEnum $property) => new NamedValue($property, $this->{$property->value}),
			$this->updatedProperties,
		);
	}
}
