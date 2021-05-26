<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

class Scope implements ScopeInterface
{
	private string $identifier;

	public function __construct(string $identifier)
	{
		$this->identifier = $identifier;
	}

	public function getIdentifier(): string
	{
		return $this->identifier;
	}

	public function isSatisfiedBy(ScopeInterface $scope): bool
	{
		return $this->getIdentifier() === $scope->getIdentifier();
	}

}
