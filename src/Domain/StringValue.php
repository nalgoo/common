<?php

namespace Nalgoo\Common\Domain;

abstract class StringValue implements StringValueInterface, \Stringable
{
	public function __toString(): string
	{
		return $this->toString();
	}
}
