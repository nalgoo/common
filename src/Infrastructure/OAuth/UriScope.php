<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

use Webmozart\Assert\Assert;

/**
 * Assuming scopes in this format
 * https://example.com/dir1/dir2/some.thing.nice
 * \-----------------/\--------/\--------------/
 *           |            |             |
 *        host    scope path prefix   scope detailed with dots
 *
 * matching scopes:
 * - https://example.com/
 * - https://example.com/dir1/
 * - https://example.com/dir1/dir2/
 * - https://example.com/dir1/dir2/some
 * - https://example.com/dir1/dir2/some.thing
 * - https://example.com/dir1/dir2/some.thing.nice
 */
class UriScope implements ScopeInterface
{
	private static ?ResourceServerConfig $defaultResourceServerConfig;

	private string $path;

	private ResourceServerConfig $resourceServerConfig;

	public function __construct(string $path, ResourceServerConfig $resourceServerConfig)
	{
		Assert::regex($path, '^[a-z0-9\.\-]+$');

		$this->path = $path;
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

	public function isSatisfiedBy(ScopeInterface $testedScope): bool
	{
		$testedScopeIdentifier = trim($testedScope->getIdentifier(), '/');

		// scope must begin with http(s)://
		if (!preg_match('/^https?:\/\//', $testedScopeIdentifier)) {
			return false;
		}

		// exact match
		if ($testedScopeIdentifier === $this->getIdentifier()) {
			return true;
		}

		if ($testedScopeIdentifier === substr($this->getIdentifier(), 0, strlen($testedScopeIdentifier))) {
			if (in_array(substr($this->getIdentifier(), strlen($testedScopeIdentifier), 1), ['.', '/'])) {
				return true;
			}
		}

		return false;
	}
}
