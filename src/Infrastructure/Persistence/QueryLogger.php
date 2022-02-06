<?php

namespace Nalgoo\Common\Infrastructure\Persistence;

use Doctrine\DBAL\Logging\SQLLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class QueryLogger implements SQLLogger
{
	private ?int $start;

	private ?string $sql;

	private ?array $params = null;

	private ?array $types = null;

	private LoggerInterface $logger;

	private string $logLevel;

	public function __construct(LoggerInterface $logger, string $logLevel = LogLevel::DEBUG)
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
				return is_resource($var) ? '__RESOURCE__' : $var;
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
