<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\DTO;

use Nalgoo\Common\Application\Interfaces\FileInterface;

class File implements FileInterface
{
	public function __construct(
		private readonly FileMeta $meta,
		private readonly string $contents,
	) {}

	public function getMeta(): FileMeta
	{
		return $this->meta;
	}

	public function getContents(): string
	{
		return $this->contents;
	}

	public function getChecksum(): string
	{
		return hash('sha3-256', $this->contents);
	}
}
