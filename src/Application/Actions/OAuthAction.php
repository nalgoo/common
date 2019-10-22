<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Actions;

use Nalgoo\Common\Infrastructure\OAuth\ScopeInterface;

abstract class OAuthAction extends Action
{
	abstract public static function getRequiredScope(): ScopeInterface;

}
