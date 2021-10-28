<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain;

use Doctrine\ORM\Mapping as ORM;
use Nalgoo\Common\Application\Interfaces\SerializerInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @OA\Schema()
 */
trait StringIdentifierTrait
{
	/**
	 * @ORM\Column(name="id", type="string", length=24, nullable=false, options={"fixed"=true, "collation"="ascii_bin"})
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 * @OA\Property()
	 */
	#[Groups([SerializerInterface::LIST_GROUP])]
	protected string $id;

	public function getId(): StringIdentifier
	{
		return StringIdentifier::fromString($this->id);
	}
}
