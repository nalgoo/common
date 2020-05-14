<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Interfaces;

interface UrlResolverInterface
{
	public function resolveUrl(string $path, array $queryParams = []): string;
}
