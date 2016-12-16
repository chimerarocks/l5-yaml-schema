<?php
namespace Test\ChimeraRocks\YamlSchema\Generators;

use ChimeraRocks\YamlSchema\Generators\MigrationGenerator;
use Tests\AbstractTestCase;

class MigrationGeneratorTest extends AbstractTestCase
{
    public function test_a_migration()
    {
        $migration = new MigrationGenerator(
            [
                'name'   => 'create_' . snake_case(str_plural('test')) . '_table',
                'fields' => 'name:string(\'name\'),family_id:integer(\'family_id\'),family_id:foreign(\'family_id\')->references(\'id\')->on(\'families\')',
            ]
        );

        $migration->getSchemaParser()->up();
    }
}