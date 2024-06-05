<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Actions;

use Lcobucci\JWT\Token;
use Nalgoo\Common\Infrastructure\OAuth\OAuthScopedInterface;
use Nalgoo\Common\Infrastructure\OAuth\ScopeInterface;
use Slim\Exception\HttpUnauthorizedException;

abstract class AuthorizedAction extends Action implements OAuthScopedInterface
{
	abstract public static function getRequiredScope(): ScopeInterface;

	protected function getAuthorizedSubject(): string
	{
		$token = $this->getToken();

		if (!$token->claims()->has('sub')) {
			throw new HttpUnauthorizedException($this->request, 'Missing `sub` claim in token');
		}

		$sub = $token->claims()->get('sub');

		if ($sub === '') {
			throw new HttpUnauthorizedException($this->request, 'Empty `sub` claim in token');
		}

		return $sub;
	}

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
	 * @deprecated use getAuthorizedSubject()
	 */
	protected function getAuthorizedUserId(): string
	{
		return $this->getAuthorizedSubject();
	}

	/**
	 * @deprecated use getAuthorizedScopes()
	 */
	protected function getRequestedScopes(): array
	{
		return $this->getAuthorizedScopes();
	}

	private function getToken(): Token
	{
		return $this->request->getAttribute('oauth_token') ?? throw new HttpUnauthorizedException($this->request, 'Missing authorization token');
	}
}
