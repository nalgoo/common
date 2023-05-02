<?php
declare(strict_types=1);

namespace Nalgoo\Common\Domain;

use Doctrine\ORM\Mapping as ORM;
use Nalgoo\Common\Application\Interfaces\SerializerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[OA\Schema]
trait SequenceIdentifierTrait
{
	#[Groups([SerializerInterface::LIST_GROUP])]
	#[ORM\Column(name: 'id', type: 'integer', length: 10, nullable: false, options: ['unsigned' => true])]
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'AUTO')]
	#[OA\Property]
	protected int $id;
}
