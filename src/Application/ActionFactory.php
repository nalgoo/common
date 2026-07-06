<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application;

use Nalgoo\Common\Application\Interfaces\ActionFactoryInterface;
use Nalgoo\Common\Application\Interfaces\UrlResolverInterface;
use Nalgoo\Common\Infrastructure\Url\UrlResolver;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

abstract class ActionFactory implements ActionFactoryInterface
{
    /** @var array<string, UrlResolverInterface> */
    protected array $urlResolvers = [];

    public function __construct(
		protected ContainerInterface $container
	)
    {
    }

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function getLogger(): LoggerInterface
    {
        $logger = $this->container->get(LoggerInterface::class);

        Assert::isInstanceOf($logger, LoggerInterface::class);

        return $logger;
    }

    public function getUrlResolver(RequestInterface $request): UrlResolverInterface
    {
        $hash = spl_object_hash($request);

        if (!array_key_exists($hash, $this->urlResolvers)) {
            $this->urlResolvers[$hash] = new UrlResolver($request);
        }

        return $this->urlResolvers[$hash];
    }

    protected function buildSerializer(\Symfony\Component\Serializer\SerializerInterface $serializer): Serializer
    {
        return new Serializer($serializer);
    }

	/**
	 * @param array<NormalizerInterface|DenormalizerInterface> $normalizers
	 * @param array<EncoderInterface|DecoderInterface>         $encoders
	 *
	 * @deprecated Use buildSerializer() instead.
	 */
    protected function createSerializer(array $normalizers = [], array $encoders = []): Serializer
    {
        return $this->buildSerializer(new \Symfony\Component\Serializer\Serializer($normalizers, $encoders));
    }
}
