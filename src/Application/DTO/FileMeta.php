<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\DTO;

use Nalgoo\Common\Application\Interfaces\FileMetaInterface;

class FileMeta implements FileMetaInterface
{
	public function __construct(
		private readonly string $name,
		private readonly int $size,
		private readonly string $contentType,
	) {}

	public function getName(): string
	{
		return $this->name;
	}

	public function getSize(): int
	{
		return $this->size;
	}

	public function getContentType(): string
	{
		return $this->contentType;
	}
}
