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
	 * Return "sub" claim from oAuth token or null, if token or claim not present
	 *
	 * @throws AuthorizationException
	 */
	protected function getAuthorizedUserId(): string
	{
		/** @var Token $token */
		$token = $this->request->getAttribute('oauth_token');

		if (!$token) {
			throw new AuthorizationException('Missing authorization token');
		}

		if (!$token->hasClaim('sub')) {
			throw new AuthorizationException('Missing `sub` claim in token');
		}

		return $token->getClaim('sub');
	}
}
