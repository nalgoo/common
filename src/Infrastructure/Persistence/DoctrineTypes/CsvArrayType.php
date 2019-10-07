<?php

namespace Nalgoo\Common\Infrastructure\Persistence\DoctrineTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\SimpleArrayType;
use Doctrine\DBAL\Types\Type;

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

		return explode(',', $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return self::NAME;
	}

	private function toCsv(array $data)
	{
		$buffer = fopen('php://memory', 'r+');

		fputcsv($buffer, $data);
		rewind($buffer);
		$formatted = fgets($buffer);
		fclose($buffer);

		return $formatted;
	}
	
	private function fromCsv(string $s)
	{
		$buffer = fopen('php://memory', 'r+');

		fwrite($buffer, $s);
		rewind($buffer);
		$data = fgetcsv($buffer);
		fclose($buffer);
		return $data;
	}

}
