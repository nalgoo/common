<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;
use Nalgoo\Common\Infrastructure\Clock\ClockService;
use Nalgoo\Common\Infrastructure\OAuth\Exceptions\OAuthScopeException;
use Nalgoo\Common\Infrastructure\OAuth\Exceptions\OAuthTokenException;
use Psr\Http\Message\ServerRequestInterface;

class ResourceServer
{
	public function __construct(
		protected Key $publicKey,
		protected ClockService $clockService
	)
	{
	}

	/**
	 * @throws OAuthScopeException
	 * @throws OAuthTokenException
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
			(array) $token->claims()->get('scopes', []),
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

		$validator = new Validator();

		try {
			$token = (new Parser(new JoseEncoder()))->parse($jwt);
		} catch (\Throwable $e) {
			throw new OAuthTokenException('Cannot parse JWT token: ' . $e->getMessage());
		}

		if (!$validator->validate($token, new SignedWith(new Sha256(), $this->publicKey))) {
			throw new OAuthTokenException('Access token signature could not be verified');
		}

		$clock = new FrozenClock($this->clockService->getCurrentTime());

		//TODO - should we use LooseValidAt or StrictValidAt ?
		if (!$validator->validate($token, new LooseValidAt($clock, new \DateInterval('PT5S')))) {
			throw new OAuthTokenException('Access token is expired');
		}

		return $token;
	}

}
