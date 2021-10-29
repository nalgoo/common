<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Actions;

use Nalgoo\Common\Application\Exceptions\DeserializeException;
use Nalgoo\Common\Application\Interfaces\ActionFactoryInterface;
use Nalgoo\Common\Application\Interfaces\SerializerInterface;
use Nalgoo\Common\Application\Interfaces\UrlResolverInterface;
use Nalgoo\Common\Application\Response\StatusCode;
use Nalgoo\Common\Domain\Exceptions\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Webmozart\Assert\Assert;

abstract class Action
{
	protected ActionFactoryInterface $actionFactory;

	protected LoggerInterface $logger;

	protected Request $request;

	protected Response $response;

	protected array $args;

	public function __construct(ActionFactoryInterface $actionFactory) {
		$this->actionFactory = $actionFactory;
		$this->logger = $actionFactory->getLogger();
	}

	/**
	 * @throws HttpNotFoundException
	 * @throws HttpBadRequestException
	 */
	public function __invoke(Request $request, Response $response, array $args): Response
	{
		$this->request = $request;
		$this->response = $response;
		$this->args = $args;

		try {
			return $this->action();
		} catch (DomainRecordNotFoundException $e) {
			throw new HttpNotFoundException($this->request, $e->getMessage());
		}
	}

	/**
	 * @throws DomainRecordNotFoundException
	 * @throws HttpBadRequestException
	 */
	abstract protected function action(): Response;

	/**
	 * @throws HttpBadRequestException
	 */
	protected function resolveArg(string $name): mixed
    {
		if (!isset($this->args[$name])) {
			throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
		}

		return $this->args[$name];
	}

	/**
	 * @throws HttpBadRequestException
	 */
	protected function getJson(): array
	{
		$body = $this->request->getBody()->getContents();

		if (trim($body) === '') {
			throw new HttpBadRequestException($this->request, 'Missing JSON input');
		}

		try {
			$input = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException) {
			throw new HttpBadRequestException($this->request, 'Malformed JSON input.');
		}

		return $input;
	}

	/**
	 * @throws HttpBadRequestException
	 */
	protected function deserializeBody(string $className): object
	{
		try {
			return $this->getSerializer()->deserialize(
				$this->request->getBody()->getContents(),
				$className
			);
		} catch (DeserializeException $e) {
			throw new HttpBadRequestException(
				$this->request,
				'Incorrect or missing input data: ' . $e->getMessage(),
				$e
			);
		}
	}

	protected function respondWithRedirect(string $uri, bool $changeToGet = false): Response
	{
		$statusCode = $changeToGet ? StatusCode::REDIRECTION_SEE_OTHER : StatusCode::REDIRECTION_TEMPORARY_REDIRECT;

		return $this->response
			->withStatus($statusCode)
			->withHeader('location', $uri);
	}

	protected function respondWithJson($data, int $statusCode = StatusCode::SUCCESS_OK, ?array $groups = null): Response
	{
		if (!is_null($groups)) {
			Assert::allStringNotEmpty($groups, 'Serializer groups must be array of strings!');
		}

		$this->response->getBody()->write($this->getSerializer()->serialize($data, $groups));

		return $this->response
			->withStatus($statusCode)
			->withHeader('Content-Type', 'application/json');
	}

	protected function setCookie(string $name, string $value, ?\DateTimeInterface $expires): static
	{
		$securePart = $this->request->getUri()->getScheme() === 'https' ? ';Secure' : '';

		$expiresPart = $expires ? (';Expires=' . $expires->format('r')) : '';

		$cookie = sprintf('%s=%s;Path=/%s;HttpOnly;SameSite=None%s', rawurlencode($name), rawurlencode($value), $expiresPart, $securePart);

		$this->response = $this->response->withAddedHeader('Set-Cookie', $cookie);

		return $this;
	}

	protected function getCookie(string $cookieName, ?string $default = null): ?string
	{
		$cookies = $this->request->getCookieParams();

		return array_key_exists($cookieName, $cookies) ? $cookies[$cookieName] : $default;
	}

	protected function getQuery(string $queryParamName, ?string $default = null): ?string
	{
		$cookies = $this->request->getQueryParams();

		return array_key_exists($queryParamName, $cookies) ? $cookies[$queryParamName] : $default;
	}

	/**
	 * Creates URI with given path (either absolute or relative) based on request's base URL
	 */
	protected function resolveUrl(string $path, array $queryParams = []): string
	{
		return $this->getUrlResolver()->resolveUrl($path, $queryParams);
	}

	protected function getUrlResolver(): UrlResolverInterface
	{
		return $this->actionFactory->getUrlResolver($this->request);
	}

	protected function getSerializer(): SerializerInterface
	{
		return $this->actionFactory->getSerializer($this->request);
	}
}
