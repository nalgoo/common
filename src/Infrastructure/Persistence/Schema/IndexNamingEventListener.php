<?php

declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Persistence\Schema;

use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

/**
 * Renames auto-generated indexes on ManyToMany join tables to follow the IX_table_column convention.
 *
 * Uses postGenerateSchema because join tables have no ClassMetadata, so per-entity events don't fire for them.
 */
class IndexNamingEventListener
{
	public static function register(EntityManager $em): void
	{
		$em->getEventManager()->addEventListener(
			[ToolEvents::postGenerateSchema],
			new self(),
		);
	}

	/**
	 * @throws SchemaException
	 */
	public function postGenerateSchema(GenerateSchemaEventArgs $args): void
	{
		$em = $args->getEntityManager();
		$schema = $args->getSchema();

		$joinTableNames = $this->collectJoinTableNames($em);

		foreach ($schema->getTables() as $table) {
			if (!in_array($table->getName(), $joinTableNames, true)) {
				continue;
			}

			foreach ($table->getIndexes() as $index) {
				if ($index->isPrimary()) {
					continue;
				}

				$newName = 'IX_' . $table->getName() . '_' . implode('_', $index->getColumns());

				if ($newName !== $index->getName()) {
					$table->renameIndex($index->getName(), $newName);
				}
			}
		}
	}

	/**
	 * @return list<string>
	 */
	private function collectJoinTableNames(EntityManagerInterface $em): array
	{
		$names = [];

		foreach ($em->getMetadataFactory()->getAllMetadata() as $metadata) {
			foreach ($metadata->getAssociationMappings() as $mapping) {
				if ($mapping['type'] === ClassMetadata::MANY_TO_MANY && isset($mapping['joinTable']['name'])) {
					$names[] = $mapping['joinTable']['name'];
				}
			}
		}

		return array_values(array_unique($names));
	}
}
