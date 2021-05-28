<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
trait IdPropertyTrait
{
	/**
	 * @ORM\Column(name="id", type="integer", length=10, nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @OA\Property()
	 */
	protected int $id;

	public function getId(): int
	{
		return $this->id;
	}

	public function getIdentifier(): IntegerIdentifier
    {
        return IntegerIdentifier::fromInt($this->getId());
    }
}
