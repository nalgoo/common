<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Serializer;

use Nalgoo\Common\Application\Interfaces\SerializerInterface;

class JsonSerializer implements SerializerInterface
{
	/**
	 * @param $data object|array|null
	 */
	public function serialize($data): string
	{
		return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

}
