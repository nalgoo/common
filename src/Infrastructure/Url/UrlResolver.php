<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Url;

use League\Uri\Uri;
use Nalgoo\Common\Application\Interfaces\UrlResolverInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class UrlResolver implements UrlResolverInterface
{
	public function __construct(
		private RequestInterface $request
	)
	{
	}

	public function resolveUrl(string|\Stringable $path, array $queryParams = []): string
	{
		$uri = self::versionAwareCreateUri($path, $this->request->getUri());

		// clear user/password from URI, which can be set in client credentials flow
		$uri = $uri->withUserInfo(null);

		if ($queryParams) {
			$uri = $uri->withQuery(http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986));
		}

		return (string) $uri;
	}

	private static function versionAwareCreateUri(string|\Stringable $uri, UriInterface $baseUri): Uri
	{
		if (method_exists(Uri::class, 'fromBaseUri')) {
			return Uri::fromBaseUri($uri, $baseUri);
		}
		return Uri::createFromBaseUri($uri, $baseUri);
	}
}
