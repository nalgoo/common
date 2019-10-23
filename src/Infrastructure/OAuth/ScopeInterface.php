<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

interface ScopeInterface
{
	public function getIdentifier(): string;

	public function isSatisfiedBy(ScopeInterface $scope): bool;

}
