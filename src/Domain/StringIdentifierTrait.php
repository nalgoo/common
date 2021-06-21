<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain;

use Doctrine\ORM\Mapping as ORM;

trait StringIdentifierTrait
{
	/**
	 * @ORM\Column(name="id", type="string", length=24, nullable=false, options={"fixed"=true, "collation"="ascii_bin"})
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	protected string $id;

	public function getId(): string
	{
		return $this->id;
	}

	public function getIdentifier(): StringIdentifier
	{
		return StringIdentifier::fromString($this->getId());
	}
}
