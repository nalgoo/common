<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

class Scope implements ScopeInterface
{
	public function __construct(
		private string $identifier
	)
	{
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
