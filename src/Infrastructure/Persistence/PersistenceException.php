<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class PersistenceException extends \Exception
{
	public static function from(\Throwable $e): self
	{
		$code = is_int($e->getCode()) ? $e->getCode() : 0;
		return match (get_class($e)) {
			ConnectionException::class => new Exceptions\ConnectionException($e->getMessage(), $code, $e),
			UniqueConstraintViolationException::class => new Exceptions\UniqueConstraintViolationException($e->getMessage(), $code, $e),
			default => new self($e->getMessage(), $code, $e),
		};
	}
}
