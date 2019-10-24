<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Middleware;

use Nalgoo\Common\Infrastructure\OAuth\Exceptions\OAuthException;
use Nalgoo\Common\Infrastructure\OAuth\OAuthScopedInterface;
use Nalgoo\Common\Infrastructure\OAuth\OAuthValidator;
use Nalgoo\Common\Infrastructure\OAuth\ResourceServer;
use Nalgoo\Common\Infrastructure\OAuth\UriPrefixedScope;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Routing\Route;

class OAuthMiddleware implements MiddlewareInterface
{
	private const CLASS_NAME_REGEX = '[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*';

	/**
	 * @var ResourceServer
	 */
	private $resourceServer;

	/**
	 * @var string|null
	 */
	private $host;

	public function __construct(ResourceServer $resourceServer)
	{
		$this->resourceServer = $resourceServer;
	}

	/**
	 * @throws HttpForbiddenException
	 * @throws \Exception
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		try {
			$route = $this->getRoute($request);

			$handlerClass = $this->getHandlerClass($route->getCallable());

			if (!$handlerClass instanceof OAuthScopedInterface) {
				throw new \Exception('Handler does not implements OAuthScopedInterface');
			}

			$requiredScope = $handlerClass::getRequiredScope();

			$this->resourceServer->validateAuthorization($request, $requiredScope);

		} catch (OAuthException $e) {
			throw new HttpUnauthorizedException($request, $e->getMessage());
		}

		return $handler->handle($request);
	}

	/**
	 * Get route object from request
	 *
	 * @throws \Exception
	 */
	private function getRoute(ServerRequestInterface $request): Route
	{
		/** @var Route $route */
		$route = $request->getAttribute('route');

		if (!$route) {
			throw new \Exception('Route attribute does not exist. Is routing middleware at the top of stack?');
		}

		if (!$route instanceof Route) {
			throw new \Exception('Route attribute is not what it seems to be.');
		}

		return $route;
	}

	/**
	 * Get class name of provided callable, if it is existing class/object, or null if it is function or Closure
	 */
	private function getHandlerClass(callable $callable): ?string
	{
		if (is_array($callable)) {
			if (is_string($callable[0])) {
				// Static class method call
				return class_exists($callable[0]) ? $callable[0] : null;
			}

			if (is_object($callable[0])) {
				// Object method call
				return get_class($callable[0]);
			}
		}

		if (is_string($callable)) {
			if (class_exists($callable)) {
				// Invokable class
				return $callable;
			}

			if (preg_match('^('.self::CLASS_NAME_REGEX.')::.+$', $callable, $matches) ) {
				if (class_exists($matches[1])) {
					return $matches[1];
				}
			}
		}

		if (is_object($callable) && !$callable instanceof \Closure) {
			// Invokable object except Closures
			return get_class($callable);
		}

		return null;
	}

}
