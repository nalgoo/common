<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\OAuth;

interface OAuthScopedInterface
{
	public static function getRequiredScope(): ScopeInterface;

}
