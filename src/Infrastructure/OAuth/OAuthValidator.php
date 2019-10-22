<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nalgoo\Common\Infrastructure\Clock\ClockService;
use Psr\Http\Message\ServerRequestInterface;

class OAuthValidator
{
	/**
	 * @var CryptKey
	 */
	protected $publicKey;

	/**
	 * @var ClockService
	 */
	private $clockService;

	/**
	 * @var string|null
	 */
	private $requiredAudience;

	public function __construct(CryptKey $publicKey, ClockService $clockService)
	{
		$this->publicKey = $publicKey;
		$this->clockService = $clockService;
	}

	public function setRequiredAudience(string $audience)
	{
		$this->requiredAudience = $audience;
	}

	/**
	 * @throws OAuthScopeException
	 * @throws OAuthTokenException
	 * @throws OAuthAudienceException
	 */
	public function validate(ServerRequestInterface $request, ?ScopeInterface $scope)
	{
		$token = $this->validateToken($request);

		$this->validateAudience($token, $this->requiredAudience);

		$this->validateScope($token, $scope);
	}

	/**
	 * @throws OAuthAudienceException
	 */
	protected function validateAudience(Token $token, ?string $requiredAudience): bool
	{
		if (!$this->requiredAudience) {
			return true;
		}

		if ($token->getClaim('aud') !== $requiredAudience) {
			throw new OAuthAudienceException();
		}

		return true;
	}

	/**
	 * @throws OAuthScopeException
	 */
	protected function validateScope(Token $token, ScopeInterface $scope): bool
	{
		$tokenScopes = array_filter(explode(' ', $token->getClaim('scopes')));

		if (!in_array($scope->getId(), $tokenScopes)) {
			throw new OAuthScopeException('Token is missing required scope - ' . $scope->getId());
		}

		return true;
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
		$data = new ValidationData($this->clockService->getCurrentTime(), 5);

		if ($token->validate($data) === false) {
			throw new OAuthTokenException('Access token is invalid');
		}

		return $token;
	}

}
