<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class PersistenceException extends \Exception
{
	public static function from(\Throwable $e): self
	{
        return match (get_class($e)) {
            ConnectionException::class => new Exceptions\ConnectionException(),
            UniqueConstraintViolationException::class => new Exceptions\UniqueConstraintViolationException(),
            default => new self(),
        };
	}
}
