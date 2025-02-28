<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

use Psr\Http\Message\RequestInterface;

class ResourceServerConfig
{
	private string $host;

	private string $scopePathPrefix = 'auth';

	public function __construct(
		string $hostName,
		private bool $secure = true,
		private ?int $port = null
	)
	{
		$this->host = trim($hostName, '/');
	}

	public static function fromRequest(RequestInterface $request): static
	{
		return new static(
			$request->getUri()->getHost(),
			$request->getUri()->getScheme() === 'https',
			$request->getUri()->getPort()
		);
	}

	public function setScopePathPrefix(string $path): void
	{
		$this->scopePathPrefix = trim('/', $path);
	}

	public function getScopeBaseUrl(): string
	{
		return $this->getSchemeAndAuthority() . '/' . $this->scopePathPrefix;
	}

	private function getSchemeAndAuthority(): string
	{
		return ($this->secure ? 'https' : 'http')
			. '://'
			. $this->host
			. ($this->port ? (':' . strval($this->port)) : '');
	}

}
