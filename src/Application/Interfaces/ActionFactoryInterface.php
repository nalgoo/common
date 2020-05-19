<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Interfaces;

use Psr\Http\Message\RequestInterface;

interface ActionFactoryInterface
{
	public function getSerializer(RequestInterface $request): SerializerInterface;

	public function getUrlResolver(RequestInterface $request): UrlResolverInterface;
}
