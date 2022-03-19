<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Middleware;

use Nalgoo\Common\Infrastructure\OAuth\Exceptions\OAuthException;
use Nalgoo\Common\Infrastructure\OAuth\OAuthScopedInterface;
use Nalgoo\Common\Infrastructure\OAuth\ResourceServer;
use Nalgoo\Common\Infrastructure\OAuth\ResourceServerConfig;
use Nalgoo\Common\Infrastructure\OAuth\UriScope;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteContext;

class OAuthMiddleware implements MiddlewareInterface
{
	private const CLASS_NAME_REGEX = '[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*';

	private ResourceServer $resourceServer;

	public function __construct(ResourceServer $resourceServer)
	{
		$this->resourceServer = $resourceServer;
	}

	/**
	 * @throws HttpUnauthorizedException
	 * @throws \Exception
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$config = ResourceServerConfig::fromRequest($request);
		UriScope::setDefaultResourceServerConfig($config);

		try {
			$route = $this->getRoute($request);

			$handlerClass = $this->getHandlerClass($route->getCallable());

			if (!$this->implements($handlerClass, OAuthScopedInterface::class)) {
				throw new \Exception('Handler does not implements OAuthScopedInterface');
			}

			/** @var OAuthScopedInterface $handlerClass */
			$requiredScope = $handlerClass::getRequiredScope();

			$token = $this->resourceServer->getValidToken($request, $requiredScope);

			$request = $request->withAttribute('oauth_token', $token);
		} catch (OAuthException $e) {
			throw new HttpUnauthorizedException($request, $e->getMessage());
		}

		return $handler->handle($request);
	}

	private function implements(string $className, string $interfaceName): bool
	{
		if (class_exists($className)) {
			$classImplements = class_implements($className);

			if (is_array($classImplements)) {
				return in_array($interfaceName, $classImplements);
			}
		}

		return false;
	}

	/**
	 * Get route object from request
	 *
	 * @throws \Exception
	 */
	private function getRoute(ServerRequestInterface $request): RouteInterface
	{
		$route = RouteContext::fromRequest($request)->getRoute();

		if (!$route) {
			throw new \Exception('Route attribute does not exist. Is routing middleware at the top of stack?');
		}

		if (!$route instanceof RouteInterface) {
			throw new \Exception('Route attribute is not what it seems to be.');
		}

		return $route;
	}

	/**
	 * Get class name of provided callable, if it is existing class/object, or null if it is function or Closure
	 *
	 * @param array|string|callable $callable
	 */
	private function getHandlerClass($callable): ?string
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

			if (preg_match('/^('.self::CLASS_NAME_REGEX.')::.+$/', $callable, $matches) ) {
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
