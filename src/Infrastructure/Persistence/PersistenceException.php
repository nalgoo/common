<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class PersistenceException extends \Exception
{
	public static function from(\Throwable $e): self
	{
		switch (get_class($e)) {
			case ConnectionException::class:
				return new Exceptions\ConnectionException();

			case UniqueConstraintViolationException::class:
				return new Exceptions\UniqueConstraintViolationException();

			default:
				return new self();
		}
	}
}
