<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

use Psr\Http\Message\RequestInterface;

class ResourceServerConfig
{
	private string $host;

	private bool $secure;

	private ?int $port;

	private string $scopePathPrefix = 'auth';

	public function __construct(string $hostName, bool $secure = true, ?int $port = null)
	{
		$this->host = trim($hostName, '/');
		$this->secure = $secure;
		$this->port = $port;
	}

	public static function fromRequest(RequestInterface $request)
	{
		return new static(
			$request->getUri()->getHost(),
			$request->getUri()->getScheme() === 'https',
			$request->getUri()->getPort()
		);
	}

	public function setScopePathPrefix(string $path)
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
