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
	private string $apiKey;

	public function __construct(string $apiKey)
	{
		$this->apiKey = $apiKey;
	}

    /**
     * @throws ApiKeyNotSetException
     * @throws InvalidApiKeyException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if (!array_key_exists('api_key', $request->getQueryParams())) {
			throw new ApiKeyNotSetException('Api key not set !');
		}

		if ($request->getQueryParams()['api_key'] !== $this->apiKey) {
			throw new InvalidApiKeyException('Invalid api key!');
		}

		return $handler->handle($request);
	}
}
