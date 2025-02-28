<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain\Enums;

use Nalgoo\Common\Domain\Exceptions\DomainLogicException;
use Nalgoo\Common\Domain\IntValueInterface;
use Nalgoo\Common\Domain\StringValueInterface;
use ReflectionClassConstant;
use Webmozart\Assert\Assert;

class Gender implements IntValueInterface, StringValueInterface, \Stringable
{
	private function __construct(
		protected mixed $value
	)
	{
		Assert::oneOf($value, static::getConstants());
	}

	protected static function getConstants(): array
	{
		$reflection = new \ReflectionClass(static::class);

		return array_values($reflection->getConstants(ReflectionClassConstant::IS_PUBLIC));
	}

	protected static function getInstanceFor($value): static
	{
		static $instances = [];

		if (!isset($instances[static::class][$value])) {
			$instances[static::class][$value] = new static($value);
		}

		return $instances[static::class][$value];
	}

	public const MALE_INT = 0;
	public const MALE_STRING = 'm';
	public const MALE_BOOL = false;
	private const MALE = [self::MALE_INT, self::MALE_STRING, self::MALE_BOOL];

	public const FEMALE_INT = 1;
	public const FEMALE_STRING = 'f';
	public const FEMALE_BOOL = true;
	private const FEMALE = [self::FEMALE_INT, self::FEMALE_STRING, self::FEMALE_BOOL];

	public const OTHER_STRING = 'x';
	private const OTHER = [self::OTHER_STRING];

	public static function fromValue(string|int|bool $value): static
	{
		return static::getInstanceFor($value);
	}

	public static function fromInt(int $value): static
	{
		return static::fromValue($value);
	}

	public static function fromString(string $value): static
	{
		return static::fromValue($value);
	}

	public static function fromBool(bool $value): static
	{
		return static::fromValue($value);
	}

	public function isFemale(): bool
	{
		return in_array($this->value, self::FEMALE);
	}

	public function isMale(): bool
	{
		return in_array($this->value, self::MALE);
	}

	public function isOther(): bool
	{
		return in_array($this->value, self::OTHER);
	}

	/**
	 * @throws DomainLogicException
	 */
	public function toInt(): int
	{
		if ($this->isMale()) {
			return self::MALE_INT;
		}
		if ($this->isFemale()) {
			return self::FEMALE_INT;
		}

		throw new DomainLogicException('Gender value ' . $this->value . ' not supported as int !');
	}

	/**
	 * @throws DomainLogicException
	 */
	public function toString(): string
	{
		if ($this->isMale()) {
			return self::MALE_STRING;
		}
		if ($this->isFemale()) {
			return self::FEMALE_STRING;
		}

		if ($this->isOther()) {
			return self::OTHER_STRING;
		}

		throw new DomainLogicException('Gender value ' . $this->value . ' not supported as string !');
	}

	/**
	 * @throws DomainLogicException
	 */
	public function __toString(): string
	{
		return $this->toString();
	}

	/**
	 * @throws DomainLogicException
	 */
	public function toBool(): bool
	{
		if ($this->isMale()) {
			return self::MALE_BOOL;
		}
		if ($this->isFemale()) {
			return self::FEMALE_BOOL;
		}

		throw new DomainLogicException('Gender value ' . $this->value . ' not supported as bool !');
	}

	/**
	 * @throws DomainLogicException
	 */
	public function asClaim(): string
	{
		if ($this->isMale()) {
			return 'male';
		}
		if ($this->isFemale()) {
			return 'female';
		}

		if ($this->isOther()) {
			return 'other';
		}

		throw new DomainLogicException('Gender value ' . $this->value . ' not supported as claim!');
	}

	public function jsonSerialize(): mixed
	{
		return $this->value;
	}
}
