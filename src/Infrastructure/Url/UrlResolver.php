<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Url;

use League\Uri\Contracts\UriInterface;
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

	public function resolveUrl(string $path, array $queryParams = []): string
	{
		$uri = self::versionAwareCreateUri($path, $this->request->getUri());

		if ($queryParams) {
			$uri = $uri->withQuery(http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986));
		}

		return (string) $uri;
	}

	private static function versionAwareCreateUri(string $uri, string|UriInterface $baseUri): Uri
	{
		if (method_exists(Uri::class, 'fromBaseUri')) {
			return Uri::fromBaseUri($uri, $baseUri);
		}
		return Uri::createFromBaseUri($uri, $baseUri);
	}
}
