<?php

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\DBAL\Logging\SQLLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class QueryLogger implements SQLLogger
{
	/**
	 * @var int|null
	 */
	private $start;

	/**
	 * @var string|null
	 */
	private $sql;

	/**
	 * @var array|null
	 */
	private $params;

	/**
	 * @var array|null
	 */
	private $types;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var string
	 */
	private $logLevel;

	public function __construct(LoggerInterface $logger, $logLevel = LogLevel::DEBUG)
	{
		$this->logger = $logger;
		$this->logLevel = $logLevel;
	}

	public function startQuery($sql, ?array $params = null, ?array $types = null)
	{
		$this->sql = $sql;
		$this->params = $params;
		$this->types = $types;
		$this->start = microtime(true);
	}

	public function stopQuery()
	{
		$duration = microtime(true) - $this->start;

		if (is_null($this->params)) {
			$this->logger->log($this->logLevel, sprintf('SQL Query %s executed in %.3fs', $this->sql, $duration));
		} else {
			$params = array_map(function ($var) {
				return is_resource($var) ? '[RESOURCE]' : $var;
			}, $this->params);

			$this->logger->log(
				$this->logLevel,
				sprintf('SQL Query %s with params %s executed in %.3fs',
					$this->sql,
					json_encode($params),
					microtime(true) - $this->start
				)
			);
		}
	}

}
