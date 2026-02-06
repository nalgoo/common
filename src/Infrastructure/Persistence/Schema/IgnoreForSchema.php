<?php
declare(strict_types=1);

namespace Nalgoo\Common\Infrastructure\Persistence\Schema;

/**
 * Annotation Class for Entities, which are ignored when comparing current db schema and orm schema (doctrine migrations diff)
 *
 * CustomSchemaProvider will filter entities when creating from schema
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class IgnoreForSchema
{

	public function __construct()
	{
	}

}
