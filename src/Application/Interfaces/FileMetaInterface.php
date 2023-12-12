<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Interfaces;

interface FileMetaInterface
{
	public function getName(): string;

	public function getSize(): int;

	public function getContentType(): string;
}
