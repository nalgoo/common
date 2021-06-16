<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\DTO;

use Nalgoo\Common\Domain\StringValueInterface;
use Webmozart\Assert\Assert;

class UpdateInput
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
	 * @param string $namedPropertyEnum name of class (Enum) which implementsStringValueInterface
	 * @noinspection PhpUndefinedMethodInspection
	 */
	public function getUpdatedProperties(string $namedPropertyEnum): array
	{
		Assert::implementsInterface($namedPropertyEnum, StringValueInterface::class);

		return array_map(
			fn(string $propName) => new NamedValue($namedPropertyEnum::fromString($propName), $this->{$propName}),
			$this->updatedProperties,
		);
	}
}
