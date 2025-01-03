<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Response;

use Psr\Http\Message\ResponseInterface;
use Slim\ResponseEmitter as SlimResponseEmitter;

class ResponseEmitter extends SlimResponseEmitter
{
	public function __construct(
		protected string $allowHeaders = 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
	{
		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	public function emit(ResponseInterface $response): void
	{
		// This variable should be set to the allowed host from which your API can be accessed with
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

		$response = $response
			->withHeader('Access-Control-Allow-Credentials', 'true')
			->withHeader('Access-Control-Allow-Origin', $origin)
			->withHeader('Access-Control-Allow-Headers', $this->allowHeaders)
			->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');

		// add no-cache directive if not already exists
		if (!$response->hasHeader('Cache-Control')) {
			$response = $response
				->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
				->withAddedHeader('Cache-Control', 'post-check=0, pre-check=0')
				->withHeader('Pragma', 'no-cache');
		}

		if (ob_get_contents()) {
			ob_clean();
		}

		parent::emit($response);
	}
}
