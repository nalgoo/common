<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application;

use Nalgoo\Common\Application\Interfaces\ActionFactoryInterface;
use Nalgoo\Common\Application\Interfaces\UrlResolverInterface;
use Nalgoo\Common\Infrastructure\Url\UrlResolver;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

abstract class ActionFactory implements ActionFactoryInterface
{
    protected ContainerInterface $container;

    protected array $urlResolvers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

	/**
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function getLogger(): LoggerInterface
    {
        return $this->container->get(LoggerInterface::class);
    }

    public function getUrlResolver(RequestInterface $request): UrlResolverInterface
    {
        $hash = spl_object_hash($request);

        if (!array_key_exists($hash, $this->urlResolvers)) {
            $this->urlResolvers[$hash] = new UrlResolver($request);
        }

        return $this->urlResolvers[$hash];
    }

    protected function createSerializer(array $normalizers = [], array $encoders = []): Serializer
    {
        return new Serializer(new \Symfony\Component\Serializer\Serializer($normalizers, $encoders));
    }
}
