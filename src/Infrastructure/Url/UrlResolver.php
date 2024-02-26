<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Url;

use League\Uri\Uri;
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

	public function resolveUrl(string|\Stringable $path, array $queryParams = []): string
	{
		$uri = Uri::fromBaseUri($path, $this->request->getUri());

		if ($queryParams) {
			$uri = $uri->withQuery(http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986));
		}

		return (string) $uri;
	}
}
