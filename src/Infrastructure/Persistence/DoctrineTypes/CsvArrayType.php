<?php

namespace Nalgoo\Common\Infrastructure\Persistence\DoctrineTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\SimpleArrayType;

class CsvArrayType extends SimpleArrayType
{
	public const NAME = 'csv_array';

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if (!$value) {
			return null;
		}

		return $this->toCsv($value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return [];
		}

		$value = is_resource($value) ? stream_get_contents($value) : $value;

		return $this->fromCsv($value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName(): string
	{
		return self::NAME;
	}

	private function toCsv(array $data): bool|string
	{
		$buffer = fopen('php://memory', 'r+');

		fputcsv($buffer, $data);
		rewind($buffer);
		$formatted = fgets($buffer);
		fclose($buffer);

		return $formatted;
	}

	private function fromCsv(string $s): bool|array
	{
		$buffer = fopen('php://memory', 'r+');

		fwrite($buffer, $s);
		rewind($buffer);
		$data = fgetcsv($buffer);
		fclose($buffer);
		return $data;
	}

}
