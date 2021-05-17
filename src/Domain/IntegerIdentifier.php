<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain;

class IntegerIdentifier
{
	private int $id;

	public function __construct(int $id)
	{
		$this->id = $id;
	}

	public static function fromInt(int $id): static
	{
		return new static($id);
	}

	public function asInt(): int
	{
		return $this->id;
	}

    public function sameAs(IntegerIdentifier $identifier): bool
    {
        return get_class($this) === get_class($identifier) && $this->id === $identifier->asInt();
    }
}
