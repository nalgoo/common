<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

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
	protected int $id;

	public function getId(): IntegerIdentifier
	{
		return IntegerIdentifier::fromInt($this->id);
	}
}
