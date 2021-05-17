<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain;

class StringIdentifier implements StringValueInterface
{
	private string $id;

	public function __construct(string $id)
	{
		$this->id = $id;
	}

    /** @noinspection PhpParameterNameChangedDuringInheritanceInspection */
    public static function fromString(string $id): static
	{
		return new static($id);
	}

	public function toString(): string
	{
		return $this->id;
	}

	public function sameAs(StringIdentifier $identifier): bool
	{
		return get_class($this) === get_class($identifier) && $this->id === $identifier->toString();
	}
}
