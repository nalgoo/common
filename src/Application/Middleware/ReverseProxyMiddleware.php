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
		$protocol = $request->getHeaderLine('X-Forwarded-Proto');

		if ($protocol === 'https') {
			$request = $request->withUri($request->getUri()->withScheme('https'));
		}

		return $handler->handle($request);
	}

}
