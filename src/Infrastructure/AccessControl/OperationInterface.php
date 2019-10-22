<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\AccessControl;

interface OperationInterface
{
	/**
	 * @return PermissionInterface[]
	 */
	public function getRequiredPermissions(): array;

}
