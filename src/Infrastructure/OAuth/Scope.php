<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

class Scope implements ScopeInterface
{
	/**
	 * @var string
	 */
	private $id;

	public function __construct(string $id)
	{
		$this->id = $id;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function isSameAs(ScopeInterface $scope): bool
	{
		return $this->getId() === $scope->getId();
	}

}
