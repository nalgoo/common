<?php

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\ORM\EntityManager;
use Nalgoo\Common\Infrastructure\Persistence\Exceptions\UniqueConstraintViolationException;

class Persister
{
	public function __construct(
		private EntityManager $entityManager
	)
	{
	}

	/**
	 * @return mixed
	 * @throws PersistenceException
	 * @throws UniqueConstraintViolationException
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	public function transaction(callable $func): mixed
	{
		try {
			return $this->entityManager->wrapInTransaction($func);
		} catch (\Throwable $e) {
			throw PersistenceException::from($e);
		}
	}

	/**
	 * @throws UniqueConstraintViolationException
	 * @throws PersistenceException
	 */
	public function flush(): void
	{
		try {
			$this->entityManager->flush();
		} catch (\Throwable $e) {
			throw PersistenceException::from($e);
		}
	}

}
