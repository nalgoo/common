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
trait SequenceIdentifierTrait
{
	/**
	 * @ORM\Column(name="id", type="integer", length=10, nullable=false, options={"unsigned"=true})
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @OA\Property()
	 */
	#[Groups([SerializerInterface::LIST_GROUP])]
	protected int $id;

	public function getId(): IntegerIdentifier
	{
		return IntegerIdentifier::fromInt($this->id);
	}
}
