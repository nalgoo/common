<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

use Webmozart\Assert\Assert;

class UriScope implements ScopeInterface
{
	private static $defaultScopePrefix = 'auth';

	private static $defaultScheme = 'https';

	private static $defaultHost;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var string
	 */
	private $scheme;

	/*
	 * @var string
	 */
	private $host;

	/**
	 * @var string
	 */
	private $prefix;

	public function __construct(string $scheme, string $host, string $prefix, string $path)
	{
		Assert::oneOf($scheme, ['http', 'https']);

		$this->scheme = $scheme;
		$this->host = self::cleanUriPart($host);
		$this->prefix =self::cleanUriPart($prefix);
		$this->path = self::cleanUriPart($path);
	}

	public static function setDefaultScopePrefix(string $prefix)
	{
		self::$defaultScopePrefix = self::cleanUriPart($prefix);
	}

	public static function setDefaultScheme(string $scheme)
	{
		Assert::oneOf($scheme, ['http', 'https']);

		self::$defaultScheme = $scheme;
	}

	public static function setDefaultHost(string $host)
	{
		self::$defaultHost = self::cleanUriPart($host);
	}

	/**
	 * @throws \Exception
	 */
	public static function fromPathWithDefaults(string $path): self
	{
		if (!self::$defaultHost) {
			throw new \Exception('Host not set, call self::setDefaultHost() first');
		}

		$scope = new self(self::$defaultScheme, self::$defaultHost, self::$defaultScopePrefix, $path);
		return $scope;
	}

	private static function cleanUriPart(string $part): string
	{
		return trim($part, '/');
	}

	public function getIdentifier(): string
	{
		$parts = [$this->host, $this->prefix, $this->path];

		return $this->scheme . '://' . implode('/', array_filter($parts));
	}

	private function getPrefix(): string
	{
		$parts = [$this->host, $this->prefix];

		return $this->scheme . '://' . implode('/', array_filter($parts));
	}

	public function isSatisfiedBy(ScopeInterface $scope): bool
	{
		return (
			preg_match('/^' . preg_quote($this->getPrefix(), '/') . '($|\/.+)/', $scope->getIdentifier()) === 1
			&&
			preg_match('/^' . preg_quote($scope->getIdentifier(), '/') . '($|\/.+)/', $this->getIdentifier()) === 1
		);
	}

}
