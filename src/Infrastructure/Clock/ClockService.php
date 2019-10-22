<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Clock;

class ClockService
{
	public function getCurrentTime(): \DateTimeImmutable
	{
		return new \DateTimeImmutable();
	}

}
