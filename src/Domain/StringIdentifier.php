<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain;

class StringIdentifier
{
	private string $id;

	public function __construct(string $id)
	{
		$this->id = $id;
	}

	public static function fromString(string $id): static
	{
		return new static($id);
	}

	public function asString(): string
	{
		return $this->id;
	}

	public function sameAs(StringIdentifier $identifier): bool
	{
		return get_class($this) === get_class($identifier) && $this->id === $identifier->asString();
	}
}
