<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Clock;

use Psr\Clock\ClockInterface;

/**
 * @deprecated Use Psr\Clock\ClockInterface directly instead
 */
class ClockService implements ClockInterface
{
	public function getCurrentTime(): \DateTimeImmutable
	{
		return new \DateTimeImmutable();
	}

	public function now(): \DateTimeImmutable
	{
		return $this->getCurrentTime();
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
