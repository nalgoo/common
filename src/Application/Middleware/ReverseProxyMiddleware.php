<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ReverseProxyMiddleware implements MiddlewareInterface
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$uri = $request->getUri();

		if ($request->hasHeader('X-Forwarded-Proto')) {
			$protocol = $request->getHeaderLine('X-Forwarded-Proto');

			if (in_array($protocol, ['http', 'https'])) {
				$uri = $uri->withScheme($protocol);
			}
		}

		if ($request->hasHeader('X-Forwarded-Host')) {
			$hostHeader = $request->getHeaderLine('X-Forwarded-Host');

			if (preg_match('/^(?P<host>[a-z0-9\.\-]+)(?::(?P<port>[0-9]+))?$/i', $hostHeader, $matches)) {
				$uri = $uri->withHost($matches['host']);

				if (array_key_exists('port', $matches)) {
					$uri->withPort((int) $matches['port']);
				}
			}
		}

		if ($request->hasHeader('X-Forwarded-Port')) {
			$port = $request->getHeaderLine('X-Forwarded-Port');

			if (ctype_digit($port)) {
				$uri = $uri->withPort((int) $port);
			}
		}

		$request = $request->withUri($uri);

		return $handler->handle($request);
	}

}
