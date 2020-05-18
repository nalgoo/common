<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Url;

use Nalgoo\Common\Application\Interfaces\UrlResolverInterface;

class Url implements \JsonSerializable
{
	private static UrlResolverInterface $urlResolver;

	private string $path;

	private array $queryParams;

	public function __construct(string $path, array $queryParams = [])
	{
		$this->path = $path;
		$this->queryParams = $queryParams;
	}

	public static function setUrlResolver(UrlResolverInterface $urlResolver)
	{
		self::$urlResolver = $urlResolver;
	}

	public static function create(string $path, array $queryParams = []): self
	{
		return new self($path, $queryParams);
	}

	public function asString(): string
	{
		if (!self::$urlResolver) {
			throw new \RuntimeException('UrlResolver not initialized, need to call setUrlResolver() first');
		}

		return self::$urlResolver->resolveUrl($this->path, $this->queryParams);
	}

	public function __toString(): string
	{
		return $this->asString();
	}

	public function jsonSerialize()
	{
		return $this->asString();
	}



}
