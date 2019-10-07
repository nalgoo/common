<?php

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;

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
		/** @noinspection PhpUnhandledExceptionInspection */
		return $this->entityManager->transactional($func);
	}

	/**
	 * @param null|object|array
	 * @throws ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function save($entity)
	{
		$this->entityManager->flush($entity);
	}

}
