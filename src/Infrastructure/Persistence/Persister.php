<?php

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException as DoctrineUniqueConstraintException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Nalgoo\Common\Infrastructure\Persistence\Exceptions\UniqueConstraintViolationException;

class Persister
{
	private EntityManager $entityManager;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @return mixed
	 * @throws PersistenceException
	 * @throws UniqueConstraintViolationException
	 * @noinspection PhpUnhandledExceptionInspection
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	public function transaction(callable $func): mixed
	{
		try {
			return $this->entityManager->wrapInTransaction($func);
		} catch (DoctrineUniqueConstraintException $e) {
			throw new UniqueConstraintViolationException($e->getMessage());
		} /** @noinspection PhpDeprecationInspection */ catch (ORMException) {
			throw new PersistenceException();
		}
	}

	/**
	 * @throws UniqueConstraintViolationException
	 * @throws PersistenceException
	 */
	public function flush()
	{
		try {
			$this->entityManager->flush();
		} /** @noinspection PhpRedundantCatchClauseInspection */ catch (DoctrineUniqueConstraintException $e) {
			throw new UniqueConstraintViolationException($e->getMessage());
		} /** @noinspection PhpDeprecationInspection */ catch (ORMException) {
			throw new PersistenceException();
		}
	}

}
