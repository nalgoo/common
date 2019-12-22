<?php

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;

abstract class DoctrineRepository
{
	protected EntityManager $entityManager;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * Find Entity by it's primary key, return null if entity does not exist
	 *
	 * @param string $entityClassName
	 * @param string|int $primaryKey
	 *
	 * @throws Exceptions\ConnectionException
	 * @throws PersistenceException
	 */
	protected function find(string $entityClassName, $primaryKey): ?object
	{
		try {
			return $this->entityManager->find($entityClassName, $primaryKey);
		} catch (\Throwable $e) {
			throw PersistenceException::from($e);
		}
	}

	/**
	 * Find and return all entities from repository
	 *
	 * @throws Exceptions\ConnectionException
	 * @throws PersistenceException
	 */
	protected function findAll(string $entityClassName): array
	{
		return $this->findBy($entityClassName, []);
	}

	/**
	 * Find and return all entities matching criteria, return empty array if no entity matches given criteria
	 *
	 * @throws Exceptions\ConnectionException
	 * @throws PersistenceException
	 */
	protected function findBy(
		string $entityClassName,
		array $criteria,
		?array $orderBy = null,
		int $limit = null,
		int $offset = null
	): array {
		try {
			$this->entityManager->getRepository($entityClassName)->findBy($criteria, $orderBy, $limit, $offset);
		} catch (\Throwable $e) {
			throw PersistenceException::from($e);
		}
	}

	/**
	 * Find and return first entity matching given criteria, return null if no entity matches given criteria
	 *
	 * @throws Exceptions\ConnectionException
	 * @throws PersistenceException
	 */
	protected function findOneBy(string $entityClassName, array $criteria, ?array $orderBy = null): ?object
	{
		try {
			$this->entityManager->getRepository($entityClassName)->findOneBy($criteria, $orderBy);
		} catch (\Throwable $e) {
			throw PersistenceException::from($e);
		}
	}

	/**
	 * @throws Exceptions\ConnectionException
	 * @throws Exceptions\UniqueConstraintViolationException
	 * @throws PersistenceException
	 */
	protected function persist(object $entity)
	{
		try {
			$this->entityManager->persist($entity);
		} catch (\Throwable $e) {
			throw PersistenceException::from($e);
		}
	}

	// todo:

	protected function queryDql(string $dql, array $params = [], ?int $limit = null, int $offset = 0): array
	{
		$query = $this->entityManager->createQuery($dql);

		foreach ($params as $key => $value) {
			$query->setParameter($key, $value);
		}

		if ($limit) {
			$query->setMaxResults($limit);
		}

		if ($offset) {
			$query->setFirstResult($offset);
		}

		$result = $query->getResult();

		return $result;
	}

	/**
	 * @return mixed
	 */
	protected function querySingleScalarDql(string $dql, array $params = [])
	{
		$query = $this->entityManager->createQuery($dql);

		foreach ($params as $key => $value) {
			$query->setParameter($key, $value);
		}

		$result = $query->getResult(Query::HYDRATE_SINGLE_SCALAR);

		return $result;
	}

}
