<?php

declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Persistence\Schema;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\Migrations\Provider\Exception\NoMappingFound;
use Doctrine\Migrations\Provider\SchemaProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Exception\NotSupported;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Add support for IgnoreForSchema attribute to ignore classes for database schema migration, changes foreign keys to always be onUpdate CASCADE
 */
final readonly class CustomSchemaProvider implements SchemaProvider
{
	public function __construct(
		private EntityManagerInterface $entityManager,
	) {
	}

	/**
	 * @throws NoMappingFound
	 * @throws NotSupported
	 * @throws SchemaException
	 */
	public function createSchema(): Schema
	{
		$metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

		if (\count($metadata) === 0) {
			throw NoMappingFound::new();
		}

		\usort($metadata, static function (ClassMetadata $a, ClassMetadata $b): int {
			return $a->getTableName() <=> $b->getTableName();
		});

		/** @var list<ClassMetadata<object>> $metadata */
		$metadata = array_filter($metadata, function (ClassMetadata $class) {
			return \count($class->getReflectionClass()->getAttributes(IgnoreForSchema::class)) === 0;
		});

		$tool = new SchemaTool($this->entityManager);

		$schema = $tool->getSchemaFromMetadata($metadata);

		// set all FK to on update cascade
		foreach ($schema->getTables() as $table) {
			$foreignKeys = $table->getForeignKeys();

			foreach ($foreignKeys as $foreignKey) {
				// copy data
				$localColumns = $foreignKey->getLocalColumns();
				$foreignTable = $foreignKey->getForeignTableName();
				$foreignColumns = $foreignKey->getForeignColumns();
				$options = $foreignKey->getOptions();
				$constraintName = $foreignKey->getName();

				// remove
				$table->removeForeignKey($constraintName);

				// set on update to cascade
				$options['onUpdate'] = 'CASCADE';

				// add with onUpdate CASCADE
				$table->addForeignKeyConstraint(
					$foreignTable,
					$localColumns,
					$foreignColumns,
					$options,
					$constraintName
				);
			}
		}

		return $schema;
	}
}
