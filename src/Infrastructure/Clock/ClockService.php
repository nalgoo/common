<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Clock;

use Psr\Clock\ClockInterface;

class ClockService implements ClockInterface
{
	/**
	 * PSR-20
	 */
	public function now(): \DateTimeImmutable
	{
		return $this->getCurrentTime();
	}

	public function getCurrentTime(): \DateTimeImmutable
	{
		return new \DateTimeImmutable();
	}

	public function getTimeMinutesAgo(int $minutes): \DateTimeImmutable
	{
		/** @noinspection PhpUnhandledExceptionInspection */
		return (new \DateTimeImmutable())->sub(new \DateInterval(sprintf('PT%dM', $minutes)));
	}

	public function getTimeMinutesAhead(int $minutes): \DateTimeImmutable
	{
		/** @noinspection PhpUnhandledExceptionInspection */
		return (new \DateTimeImmutable())->add(new \DateInterval(sprintf('PT%dM', $minutes)));
	}
}
