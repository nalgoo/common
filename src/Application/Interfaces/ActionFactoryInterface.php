<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Interfaces;

use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Contains common dependencies for Actions
 * Also, Serializer and UrlResolver are request-related
 */
interface ActionFactoryInterface
{
	public function getLogger(): LoggerInterface;

	public function getSerializer(RequestInterface $request): SerializerInterface;

	public function getUrlResolver(RequestInterface $request): UrlResolverInterface;
}
