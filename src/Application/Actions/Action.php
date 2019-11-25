<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Actions;

use League\Uri\Http;
use Nalgoo\Common\Application\Exceptions\DeserializeException;
use Nalgoo\Common\Application\Interfaces\SerializerInterface;
use Nalgoo\Common\Application\Response\StatusCode;
use Nalgoo\Common\Domain\Exceptions\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

abstract class Action
{
	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var Response
	 */
	protected $response;

	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var SerializerInterface
	 */
	private $serializer;

	public function __construct(LoggerInterface $logger, SerializerInterface $serializer)
	{
		$this->logger = $logger;
		$this->serializer = $serializer;
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 * @param array    $args
	 * @return Response
	 * @throws HttpNotFoundException
	 * @throws HttpBadRequestException
	 */
	public function __invoke(Request $request, Response $response, $args): Response
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
	 * @return Response
	 * @throws DomainRecordNotFoundException
	 * @throws HttpBadRequestException
	 */
	abstract protected function action(): Response;

	/**
	 * @param  string $name
	 * @return mixed
	 * @throws HttpBadRequestException
	 */
	protected function resolveArg(string $name)
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
		} catch (\JsonException $e) {
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
			return $this->serializer->deserialize(
				$this->request->getBody()->getContents(),
				$className
			);
		} catch (DeserializeException $e) {
			throw new HttpBadRequestException($this->request, 'Incorrect or missing input data');
		}
	}

	/**
	 * @param  array|object|null $data
	 * @deprecated
	 * @return Response
	 */
	protected function respondWithData($data = null): Response
	{
		$payload = new ActionPayload(200, $data);
		return $this->respond($payload);
	}

	/**
	 * @param ActionPayload $payload
	 * @deprecated
	 * @return Response
	 */
	protected function respond(ActionPayload $payload): Response
	{
		$json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		$this->response->getBody()->write($json);
		return $this->response->withHeader('Content-Type', 'application/json');
	}

	protected function respondWithRedirect(string $uri, bool $changeToGet = false): Response
	{
		$statusCode = $changeToGet ? StatusCode::REDIRECTION_SEE_OTHER : StatusCode::REDIRECTION_TEMPORARY_REDIRECT;

		return $this->response
			->withStatus($statusCode)
			->withHeader('location', $uri);
	}

	protected function respondWithJson($data, int $statusCode = 200): Response
	{
		$json = $this->serializer->serialize($data);

		$this->response->getBody()->write($json);

		return $this->response
			->withStatus($statusCode)
			->withHeader('Content-Type', 'application/json');
	}

	protected function setCookie(string $name, string $value, ?\DateTimeInterface $expires)
	{
		$securePart = $this->request->getUri()->getScheme() === 'https' ? ';Secure' : '';

		$expiresPart = $expires ? (';Expires=' . $expires->format('r')) : '';

		$cookie = sprintf('%s=%s;Path=/%s;HttpOnly;SameSite=None%s', rawurlencode($name), rawurlencode($value), $expiresPart, $securePart);

		$this->response = $this->response->withAddedHeader('Set-Cookie', $cookie);
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
	protected function resolveUri(string $path, array $queryParams = []): string
	{
		$uri = Http::createFromBaseUri($path, $this->request->getUri());

		if ($queryParams) {
			$uri = $uri->withQuery(http_build_query($queryParams, '', '&'));
		}

		return (string) $uri;
	}

}
