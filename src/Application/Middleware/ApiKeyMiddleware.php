<?php

namespace Nalgoo\Common\Application\Middleware;

use Nalgoo\Common\Application\Middleware\Exceptions\ApiKeyNotSetException;
use Nalgoo\Common\Application\Middleware\Exceptions\InvalidApiKeyException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Used for routes with elemental string api key
 * DO NOT USE FOR ROUTES WITH SENSITIVE INFORMATION
 **/
class ApiKeyMiddleware implements MiddlewareInterface
{
	public function __construct(
		protected string $apiKey
	)
	{
	}

	/**
	 * @throws ApiKeyNotSetException
	 */
	protected function getRequestApiKey(ServerRequestInterface $request): string
	{
		if ($request->hasHeader('X-Api-Key')) {
			return $request->getHeaderLine('X-Api-Key');
		}

		if (array_key_exists('api_key', $request->getQueryParams())) {
			return $request->getQueryParams()['api_key'];
		}

		throw new ApiKeyNotSetException('API key not set !');
	}

    /**
     * @throws ApiKeyNotSetException
     * @throws InvalidApiKeyException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if ($this->getRequestApiKey($request) !== $this->apiKey) {
			throw new InvalidApiKeyException('Invalid api key!');
		}

		return $handler->handle($request);
	}
}
