<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Actions;

use Lcobucci\JWT\Token;
use Nalgoo\Common\Application\Exceptions\AuthorizationException;
use Nalgoo\Common\Infrastructure\OAuth\OAuthScopedInterface;
use Nalgoo\Common\Infrastructure\OAuth\ScopeInterface;

abstract class AuthorizedAction extends Action implements OAuthScopedInterface
{
	abstract public static function getRequiredScope(): ScopeInterface;

	/**
	 * @throws AuthorizationException
	 */
	protected function getAuthorizedSubject(): string
	{
		$token = $this->getToken();

		if (!$token->claims()->has('sub')) {
			throw new AuthorizationException('Missing `sub` claim in token');
		}

		$sub = $token->claims()->get('sub');

		if ($sub === '') {
			throw new AuthorizationException('Empty `sub` claim in token');
		}

		return $sub;
	}

	/**
	 * @return string[]
	 * @throws AuthorizationException
	 */
	protected function getAuthorizedScopes(): array
	{
		$claims = $this->getToken()->claims();

		if ($claims->has('scope')) {
			return array_filter(array_map('trim', explode(' ', $claims->get('scope'))));
		}

		return $claims->get('scopes', []);
	}

	/**
	 * Return "sub" claim from oAuth token or throw AuthorizationException if not set or empty
	 *
	 * @throws AuthorizationException
	 * @deprecated use getAuthorizedSubject()
	 */
	protected function getAuthorizedUserId(): string
	{
		return $this->getAuthorizedSubject();
	}

	/**
	 * @throws AuthorizationException
	 * @deprecated use getAuthorizedScopes()
	 */
	protected function getRequestedScopes(): array
	{
		return $this->getAuthorizedScopes();
	}

	/**
	 * @throws AuthorizationException
	 */
	private function getToken(): Token
	{
		/** @var Token $token */
		$token = $this->request->getAttribute('oauth_token');

		if (!$token) {
			throw new AuthorizationException('Missing authorization token');
		}

		return $token;
	}
}
