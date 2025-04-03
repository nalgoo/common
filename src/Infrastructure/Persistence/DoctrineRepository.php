<?php

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

abstract class DoctrineRepository
{
	public function __construct(
		protected EntityManager $entityManager
	)
	{
	}

	/**
	 * Find Entity by its primary key, return null if entity does not exist
	 * @template TObject of object
	 * @param class-string<TObject> $entityClassName
	 * @param mixed $primaryKey
	 * @return TObject|null
	 * @throws Exceptions\ConnectionException
	 * @throws PersistenceException
	 */
	protected function find(string $entityClassName, mixed $primaryKey): ?object
	{
		try {
			return $this->entityManager->find($entityClassName, $primaryKey);
		} catch (\Throwable $e) {
			throw PersistenceException::from($e);
		}
	}

	/**
	 * Find and return all entities from repository
	 * @template TObject of object
	 * @param class-string<TObject> $entityClassName
	 * @return TObject[]
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
	 * @template TObject of object
	 * @param class-string<TObject> $entityClassName
	 * @return TObject[]
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
	): array
	{
		try {
			return $this->entityManager->getRepository($entityClassName)->findBy($criteria, $orderBy, $limit, $offset);
		} catch (\Throwable $e) {
			throw PersistenceException::from($e);
		}
	}

	/**
	 * Find and return first entity matching given criteria, return null if no entity matches given criteria
	 * @template TObject of object
	 * @param class-string<TObject> $entityClassName
	 * @return TObject|null
	 *
	 * @throws Exceptions\ConnectionException
	 * @throws PersistenceException
	 */
	protected function findOneBy(string $entityClassName, array $criteria, ?array $orderBy = null): ?object
	{
		try {
			return $this->entityManager->getRepository($entityClassName)->findOneBy($criteria, $orderBy);
		} catch (\Throwable $e) {
			throw PersistenceException::from($e);
		}
	}

	/**
	 * @throws Exceptions\ConnectionException
	 * @throws Exceptions\UniqueConstraintViolationException
	 * @throws PersistenceException
	 */
	protected function persist(object $entity): void
	{
		try {
			$this->entityManager->persist($entity);
		} catch (\Throwable $e) {
			throw PersistenceException::from($e);
		}
	}

	/**
	 * @throws Exceptions\ConnectionException
	 * @throws PersistenceException
	 */
	protected function remove(object $entity): void
	{
		try {
			$this->entityManager->remove($entity);
		} catch (\Throwable $e) {
			throw PersistenceException::from($e);
		}
	}

	protected function queryDql(string $dql, array $params = [], ?int $limit = null, int $offset = 0): mixed
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

		return $query->getResult();
	}

	protected function querySingleScalarDql(string $dql, array $params = []): mixed
	{
		$query = $this->entityManager->createQuery($dql);

		foreach ($params as $key => $value) {
			$query->setParameter($key, $value);
		}

		return $query->getResult(Query::HYDRATE_SINGLE_SCALAR);
	}

}
