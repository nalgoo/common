<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Interfaces;

interface SerializerInterface
{
	/**
	 * @param $data object|array|null
	 */
	public function serialize($data): string;

}
