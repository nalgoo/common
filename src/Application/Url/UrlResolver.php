<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Url;

use League\Uri\Http;
use Nalgoo\Common\Application\Interfaces\UrlResolverInterface;
use Psr\Http\Message\RequestInterface;

class UrlResolver implements UrlResolverInterface
{
	/**
	 * @var RequestInterface
	 */
	private RequestInterface $request;

	public function __construct(RequestInterface $request)
	{
		$this->request = $request;
	}

	public function resolveUrl(string $path, array $queryParams = []): string
	{
		$uri = Http::createFromBaseUri($path, $this->request->getUri());

		if ($queryParams) {
			$uri = $uri->withQuery(http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986));
		}

		return (string) $uri;
	}

}
