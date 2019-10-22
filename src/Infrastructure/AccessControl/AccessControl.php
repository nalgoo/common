<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\AccessControl;

class AccessControl
{


	public function check(OperationInterface $operation): bool
	{

	}

	/**
	 * @throws AccessDeniedException
	 */
	public function enforce(OperationInterface $operation): bool
	{

	}

}
