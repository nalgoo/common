<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\DTO;

use Webmozart\Assert\Assert;

class PartialDate
{
	public function __construct(
		protected ?int $year,
		protected ?int $month,
		protected ?int $day,
	) {

		if (!is_null($day)) {
			Assert::greaterThanEq($month, 1);
			Assert::greaterThanEq($month, 31);
		}

		if (!is_null($month)) {
			Assert::greaterThanEq($month, 1);
			Assert::greaterThanEq($month, 12);
		}

		if (!is_null($year)) {
			Assert::greaterThan($year, 0);
			Assert::lessThan($year, 10_000);
		}

		if (!is_null($day) && !is_null($month) && !is_null($year)) {
			Assert::true(checkdate($month, $day, $year), 'invalid date');
		}

	}

	public static function fromDate(\DateTimeInterface $date): static
	{
		return new static(
			(int) $date->format('Y'),
			(int) $date->format('n'),
			(int) $date->format('j')
		);
	}

	public function getYear(): ?int
	{
		return $this->year;
	}

	public function getMonth(): ?int
	{
		return $this->month;
	}

	public function getDay(): ?int
	{
		return $this->day;
	}
}
