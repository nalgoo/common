<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use League\OAuth2\Server\CryptKey;
use Nalgoo\Common\Infrastructure\Clock\ClockService;
use Nalgoo\Common\Infrastructure\OAuth\Exceptions\OAuthAudienceException;
use Nalgoo\Common\Infrastructure\OAuth\Exceptions\OAuthScopeException;
use Nalgoo\Common\Infrastructure\OAuth\Exceptions\OAuthTokenException;
use Psr\Http\Message\ServerRequestInterface;

class ResourceServer
{
	private CryptKey $publicKey;

	private ClockService $clockService;

	public function __construct(CryptKey $publicKey, ClockService $clockService)
	{
		$this->publicKey = $publicKey;
		$this->clockService = $clockService;
	}

	/**
	 * @throws OAuthTokenException
	 * @throws OAuthAudienceException
	 * @throws OAuthScopeException
	 */
	public function getValidToken(ServerRequestInterface $request, ScopeInterface $requiredScope): Token
	{
		$token = $this->validateToken($request);

		$this->validateScope($token, $requiredScope);

		return $token;
	}

	/**
	 * @throws OAuthScopeException
	 */
	protected function validateScope(Token $token, ScopeInterface $requiredScope): bool
	{
		$scopes = array_map(
			fn ($scopeIdentifier) => new Scope($scopeIdentifier),
			(array) $token->getClaim('scopes'),
		);

		foreach ($scopes as $scope) {
			if ($requiredScope->isSatisfiedBy($scope)) {
				return true;
			}
		}

		throw new OAuthScopeException('Token is missing required scope: ' . $requiredScope->getIdentifier());
	}

	/**
	 * Taken from https://oauth2.thephpleague.com/
	 *
	 * @throws OAuthTokenException
	 */
	protected function validateToken(ServerRequestInterface $request): Token
	{
		if ($request->hasHeader('authorization') === false) {
			throw new OAuthTokenException('Missing "Authorization" header');
		}

		$header = $request->getHeader('authorization');
		$jwt = trim((string) preg_replace('/^(?:\s+)?Bearer\s/', '', $header[0]));

		// Attempt to parse and validate the JWT

		try {
			$token = (new Parser())->parse($jwt);
		} catch (\Throwable $e) {
			throw new OAuthTokenException('Cannot parse JWT token: ' . $e->getMessage());
		}

		try {
			if ($token->verify(new Sha256(), $this->publicKey->getKeyPath()) === false) {
				throw new OAuthTokenException('Access token could not be verified');
			}
		} catch (\BadMethodCallException $exception) {
			throw new OAuthTokenException('Access token is not signed');
		}

		// Ensure access token hasn't expired
		$data = new ValidationData($this->clockService->getCurrentTime()->getTimestamp(), 5);

		if ($token->validate($data) === false) {
			throw new OAuthTokenException('Access token is invalid');
		}

		return $token;
	}

}
