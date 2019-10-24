<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

class ResourceServerConfig
{
	/**
	 * @var string
	 */
	private $host;

	/**
	 * @var bool
	 */
	private $secure;

	/**
	 * @var string
	 */
	private $scopePathPrefix = 'auth';

	public function __construct(string $host, bool $secure = true)
	{
		$this->host = trim('/', $host);
		$this->secure = $secure;
	}

	public function setScopePathPrefix(string $path)
	{
		$this->scopePathPrefix = trim('/', $path);
	}

	public function getAudience(): string
	{
		return $this->getHost();
	}

	public function getScopeBaseUrl(): string
	{
		return $this->getHost() . '/' . $this->scopePathPrefix;
	}

	private function getHost(): string
	{
		return ($this->secure ? 'https' : 'http') . '://' . $this->host;
	}

}
