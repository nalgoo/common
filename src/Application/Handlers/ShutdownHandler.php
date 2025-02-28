<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Handlers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\ResponseEmitter;

class ShutdownHandler
{

	public function __construct(
		private Request $request,
		private HttpErrorHandler $errorHandler,
		private bool $displayErrorDetails
	) {
	}

	public function __invoke(): void
	{
		$error = error_get_last();

		if ($error) {
			$exception = new \ErrorException($error['message'], $error['type'], 1, $error['file'], $error['line']);
			$response = $this->errorHandler->__invoke($this->request, $exception, $this->displayErrorDetails, false, false);

			$responseEmitter = new ResponseEmitter();
			$responseEmitter->emit($response);
		}
	}

}
