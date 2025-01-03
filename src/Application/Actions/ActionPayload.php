<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Actions;

use JsonSerializable;
use Nalgoo\Common\Application\Response\StatusCode;

class ActionPayload implements JsonSerializable
{
	public function __construct(
		private int $statusCode = StatusCode::SUCCESS_OK,
		private array|object|null $data = null,
		private ?ActionError $error = null
	) {
	}

	public function getStatusCode(): int
	{
		return $this->statusCode;
	}

	public function getData(): array|object|null
	{
		return $this->data;
	}

	public function getError(): ?ActionError
	{
		return $this->error;
	}

	public function jsonSerialize(): array
	{
		$payload = [
			'statusCode' => $this->statusCode,
		];

		if ($this->data !== null) {
			$payload['data'] = $this->data;
		} elseif ($this->error !== null) {
			$payload['error'] = $this->error;
		}

		return $payload;
	}
}
