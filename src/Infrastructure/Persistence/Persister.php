<?php

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException as DoctrineUniqueConstraintException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Nalgoo\Common\Infrastructure\Persistence\Exceptions\PersistenceException;
use Nalgoo\Common\Infrastructure\Persistence\Exceptions\UniqueConstraintViolationException;

class Persister
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @return bool|mixed
	 */
	public function transaction(callable $func)
	{
//		/** @noinspection PhpUnhandledExceptionInspection */
//		return $this->entityManager->transactional($func);

		try {
			return $this->entityManager->transactional($func);

		} catch (DoctrineUniqueConstraintException $e) {
			throw new UniqueConstraintViolationException($e->getMessage());
		} catch (\Throwable $e) {
			throw new PersistenceException();
		}
	}

	/**
	 * @param object|array
	 * @throws UniqueConstraintViolationException
	 * @throws PersistenceException
	 */
	public function save($entity)
	{
		try {
			$this->entityManager->flush($entity);
		} catch (DoctrineUniqueConstraintException $e) {
			throw new UniqueConstraintViolationException($e->getMessage());
		} catch (\Throwable $e) {
			throw new PersistenceException();
		}
	}

}
