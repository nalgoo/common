<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

use Psr\Http\Message\RequestInterface;

class ResourceServerConfig
{
	private string $host;

	private bool $secure;

	private string $scopePathPrefix = 'auth';

	public function __construct(string $host, bool $secure = true)
	{
		$this->host = trim($host, '/');
		$this->secure = $secure;
	}

	public static function fromRequest(RequestInterface $request)
	{
		return new static($request->getUri()->getHost(), $request->getUri()->getScheme() === 'https');
	}

	public function setScopePathPrefix(string $path)
	{
		$this->scopePathPrefix = trim('/', $path);
	}

	/**
	 * @deprecated
	 */
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
