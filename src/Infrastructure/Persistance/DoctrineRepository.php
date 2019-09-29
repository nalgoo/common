<?php

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

abstract class DoctrineRepository
{
	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

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
