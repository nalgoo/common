<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Interfaces;

use Nalgoo\Common\Application\Exceptions\DeserializeException;

interface SerializerInterface
{
	/**
	 * @param $data object|array|null
	 */
	public function serialize($data): string;

	/**
	 * Deserialize string into an object of supplied class name
	 *
	 * @throws DeserializeException
	 */
	public function deserialize(string $data, string $className): object;

}
