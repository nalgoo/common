<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Interfaces;

interface FileInterface
{
	public function getMeta(): FileMetaInterface;

	public function getContents(): string;
}
