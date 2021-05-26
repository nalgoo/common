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
	 * Return "sub" claim from oAuth token or throw AuthorizationException if not set or empty
	 *
	 * @throws AuthorizationException
	 */
	protected function getAuthorizedUserId(): string
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
     * @throws AuthorizationException
     */
    protected function getRequestedScopes(): array
	{
		return $this->getToken()->claims()->get('scopes', []);
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
