<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

class UriPrefixedScope implements ScopeInterface
{
	/**
	 * @var ResourceServerConfig|null
	 */
	private static $defaultResourceServerConfig;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var ResourceServerConfig
	 */
	private $resourceServerConfig;

	public function __construct(string $path, ResourceServerConfig $resourceServerConfig)
	{
		$this->path = trim($path, '/');
		$this->resourceServerConfig = $resourceServerConfig;
	}

	public static function setDefaultResourceServerConfig(ResourceServerConfig $resourceServerConfig)
	{
		self::$defaultResourceServerConfig = $resourceServerConfig;
	}

	/**
	 * @throws \RuntimeException
	 */
	public static function withDefaults(string $path): self
	{
		if (!self::$defaultResourceServerConfig) {
			throw new \RuntimeException('ResourceServerConfig not set, call self::setDefaultResourceServerConfig() first');
		}

		$scope = new self($path, self::$defaultResourceServerConfig);

		return $scope;
	}

	public function getIdentifier(): string
	{
		return $this->resourceServerConfig->getScopeBaseUrl() . '/' . $this->path;
	}

	public function isSatisfiedBy(ScopeInterface $scope): bool
	{
		return (
			preg_match('/^' . preg_quote($this->resourceServerConfig->getScopeBaseUrl(), '/') . '($|\/.+)/', $scope->getIdentifier()) === 1
			&&
			preg_match('/^' . preg_quote($scope->getIdentifier(), '/') . '($|\/.+)/', $this->getIdentifier()) === 1
		);
	}

}
