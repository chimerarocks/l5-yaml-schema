<?php
namespace Test\ChimeraRocks\YamlSchema\Yaml;

use ChimeraRocks\YamlSchema\Yaml\YamlParser;
use ChimeraRocks\YamlSchema\Yaml\YamlSchema;
use Tests\AbstractTestCase;

class YamlSchemaTest extends AbstractTestCase
{
    public function test_if_can_get_migration_from_a_super_simple_schema()
    {
        $schema = $this->getBasicResult('super_simple_schema');
        $migrations = $schema->getMigrations();
        $expectedOptions = 'name:string(\'name\'),address:string(\'address\',255)';

        $this->assertEquals($expectedOptions, $migrations['User']['options']);
        $this->assertEquals('User', $migrations['User']['name']);
        $this->assertEmpty($schema->getRelations());
    }

    public function test_if_can_parse_a_file()
    {
        $schema = $this->getBasicResult('simple_schema');
        $migrations = $schema->getMigrations();
        $expectedOptions = 'name:string(\'name\'),age:integer(\'age\')->default(\'26\')->unsigned(\'\'),address:string(\'address\',255)';

        $this->assertEquals($expectedOptions, $migrations['User']['options']);
        $this->assertEquals('User', $migrations['User']['name']);
        $this->assertEmpty($schema->getRelations());
    }

    public function test_if_can_parse_a_file_with_hasOne_relationship()
    {
        $schema = $this->getBasicResult('has_one_schema');
        $expectedOptions = 'name:string(\'name\'),address:string(\'address\',255)';
        $expectedRelation = [
            'hasOne' => [
                [
                    'entity' => 'Family'
                ]
            ]
        ];

        $migrations = $schema->getMigrations();
        $relations = $schema->getRelations();
        $this->assertEquals($expectedOptions, $migrations['User']['options']);
        $this->assertEquals('User', $migrations['User']['name']);
        $this->assertEquals($expectedRelation, $relations['User']['relations']);
        $this->assertEquals('User', $relations['User']['name']);
    }

    public function test_if_can_parse_a_file_with_hasMany_relationship()
    {
        $schema = $this->getBasicResult('has_many_schema');
        $expectedOptions = 'name:string(\'name\'),address:string(\'address\',255)';

        $expectedRelation = [
            'hasMany' => [
                [
                    'entity' => 'User'
                ]
            ]
        ];

        $migrations = $schema->getMigrations();
        $relations = $schema->getRelations();
        $this->assertEquals($expectedOptions, $migrations['Family']['options']);
        $this->assertEquals('Family', $migrations['Family']['name']);
        $this->assertEquals($expectedRelation, $relations['Family']['relations']);
        $this->assertEquals('Family', $relations['Family']['name']);
    }

    public function test_if_can_parse_a_file_with_belongsTo_relationship()
    {
        $schema = $this->getBasicResult('belongs_to_schema');
        $expectedOptions = 'name:string(\'name\'),family_id:foreign(\'family_id\')->references(\'id\')->on(\'families\')';

        $expectedRelation = [
            'belongsTo' => [
                [
                    'entity' => 'Family'
                ]
            ]
        ];

        $migrations = $schema->getMigrations();
        $relations = $schema->getRelations();
        $this->assertEquals($expectedOptions, $migrations['User']['options']);
        $this->assertEquals('User', $migrations['User']['name']);
        $this->assertEquals($expectedRelation, $relations['User']['relations']);
        $this->assertEquals('User', $relations['User']['name']);
    }

    public function test_if_can_parse_a_file_with_belongsTo_with_dinamic_fields_relationship()
    {
        $schema = $this->getBasicResult('belongs_to_dinamic_schema');
        $expectedOptions = 'name:string(\'name\'),brother_name:foreign(\'brother_name\')->references(\'name\')->on(\'tribes\')';

        $expectedRelation = [
            'belongsTo' => [
                [
                    'entity' => 'Family',
                    'field' => 'brother_name',
                    'references' => 'name',
                    'table' => 'tribes'
                ]
            ]
        ];

        $migrations = $schema->getMigrations();
        $relations = $schema->getRelations();
        $this->assertEquals($expectedOptions, $migrations['User']['options']);
        $this->assertEquals('User', $migrations['User']['name']);
        $this->assertEquals($expectedRelation, $relations['User']['relations']);
        $this->assertEquals('User', $relations['User']['name']);
    }

    public function test_if_can_parse_a_file_with_belongsToMany()
    {
        $schema = $this->getBasicResult('belongs_to_many_schema');
        $expectedOptions = 'name:string(\'name\')';

        $expectedRelation = [
            'belongsToMany' => [
                [
                    'entity' => 'Project',
                ]
            ]
        ];

        $migrations = $schema->getMigrations();
        $relations = $schema->getRelations();
        $this->assertEquals($expectedOptions, $migrations['User']['options']);
        $this->assertEquals('User', $migrations['User']['name']);
        $this->assertEquals($expectedRelation, $relations['User']['relations']);
        $this->assertEquals('User', $relations['User']['name']);
    }

    public function test_if_can_parse_a_file_with_belongsToMany_with_dinamic()
    {
        $schema = $this->getBasicResult('belongs_to_many_dinamic_schema');
        $expectedOptions = 'name:string(\'name\')';
        $expectedRelation = [
            'belongsToMany' => [
                [
                    'entity' => 'Family',
                    'joining_table' => 'tribes',
                    'own' => 'tribe_name',
                    'inversed' => 'family_name'
                ]
            ]
        ];

        $migrations = $schema->getMigrations();
        $relations = $schema->getRelations();
        $this->assertEquals($expectedOptions, $migrations['User']['options']);
        $this->assertEquals('User', $migrations['User']['name']);
        $this->assertEquals($expectedRelation, $relations['User']['relations']);
        $this->assertEquals('User', $relations['User']['name']);
    }

    public function test_if_can_parse_a_file_with_two_entities_schema()
    {
        $schema = $this->getBasicResult('two_entities_schema');
        $expectedUserOptions = 'name:string(\'name\'),address:string(\'address\',255)';
        $expectedUserRelation = [
            'hasOne' => [
                [
                    'entity' => 'Family'
                ]
            ]
        ];
        $expectedFamilyOptions = 'name:string(\'name\'),address:string(\'address\',255)';
        $expectedFamilyRelation = [
            'hasMany' => [
                [
                    'entity' => 'User'
                ]
            ]
        ];

        $migrations = $schema->getMigrations();
        $this->assertCount(2, $migrations);
        $relations = $schema->getRelations();
        $this->assertCount(2, $relations);
        $this->assertEquals($expectedUserOptions, $migrations['User']['options']);
        $this->assertEquals('User', $migrations['User']['name']);
        $this->assertEquals($expectedUserRelation, $relations['User']['relations']);
        $this->assertEquals('User', $relations['User']['name']);
        $this->assertEquals($expectedFamilyOptions, $migrations['Family']['options']);
        $this->assertEquals('Family', $migrations['Family']['name']);
        $this->assertEquals($expectedFamilyRelation, $relations['Family']['relations']);
        $this->assertEquals('Family', $relations['Family']['name']);
    }

    public function test_if_can_parse_a_file_with_two_entities_and_relations_schema()
    {
        $schema = $this->getBasicResult('two_entities_and_relations_schema');
        $expectedUserOptions = 'name:string(\'name\'),address:string(\'address\',255),family_id:foreign(\'family_id\')->references(\'id\')->on(\'families\')';
        $expectedUserRelation = [
            'hasOne' => [
                [
                    'entity' => 'Car'
                ],
                [
                    'entity' => 'Job'
                ]
            ],
            'belongsTo' => [
                [
                    'entity' => 'Family'
                ]
            ]
        ];
        $expectedFamilyOptions = 'name:string(\'name\'),address:string(\'address\',255)';
        $expectedFamilyRelation = [
            'hasMany' => [
                [
                    'entity' => 'User'
                ]
            ]
        ];

        $migrations = $schema->getMigrations();
        $this->assertCount(2, $migrations);
        $relations = $schema->getRelations();
        $this->assertCount(2, $relations);
        $this->assertEquals($expectedUserOptions, $migrations['User']['options']);
        $this->assertEquals('User', $migrations['User']['name']);
        $this->assertEquals($expectedUserRelation, $relations['User']['relations']);
        $this->assertEquals('User', $relations['User']['name']);
        $this->assertEquals($expectedFamilyOptions, $migrations['Family']['options']);
        $this->assertEquals('Family', $migrations['Family']['name']);
        $this->assertEquals($expectedFamilyRelation, $relations['Family']['relations']);
        $this->assertEquals('Family', $relations['Family']['name']);
    }

    public function test_if_can_attempt_that_has_relation_needs_inversed_field()
    {

    }

    public function test_parsing_multiple_files()
    {
        $file1 = $this->getStubsFile('two_entities_and_relations_schema');
        $file2 = $this->getStubsFile('car_schema');
        $parser = new YamlParser($file1);
        $entities1 = $parser->parse();
        $entities2 = $parser->parse($file2);
        $schema = new YamlSchema();
        foreach ($entities1 as $entity) {
            $schema->addEntity($entity);
        }
        foreach ($entities2 as $entity) {
            $schema->addEntity($entity);
        }
        $schema->build();

        $expectedUserOptions = 'name:string(\'name\'),address:string(\'address\',255),family_id:foreign(\'family_id\')->references(\'id\')->on(\'families\')';
        $expectedUserRelation = ['hasOne' => [['entity' => 'Car'],['entity' => 'Job']],'belongsTo' => [['entity' => 'Family']]];
        $expectedFamilyOptions = 'name:string(\'name\'),address:string(\'address\',255)';
        $expectedFamilyRelation = ['hasMany' => [['entity' => 'User']]];
        $expectedCarOptions = 'name:string(\'name\'),address:string(\'address\',255),user_id:foreign(\'user_id\')->references(\'id\')->on(\'users\')';
        $expectedCarRelation = ['belongsTo' => [['entity' => 'User']]];

        $migrations = $schema->getMigrations();

        $this->assertCount(3, $migrations);
        $relations = $schema->getRelations();
        $this->assertCount(3, $relations);
        $this->assertEquals($expectedUserOptions, $migrations['User']['options']);
        $this->assertEquals('User', $migrations['User']['name']);
        $this->assertEquals($expectedUserRelation, $relations['User']['relations']);
        $this->assertEquals('User', $relations['User']['name']);
        $this->assertEquals($expectedFamilyOptions, $migrations['Family']['options']);
        $this->assertEquals('Family', $migrations['Family']['name']);
        $this->assertEquals($expectedFamilyRelation, $relations['Family']['relations']);
        $this->assertEquals('Family', $relations['Family']['name']);
        $this->assertEquals($expectedCarOptions, $migrations['Car']['options']);
        $this->assertEquals('Car', $migrations['Car']['name']);
        $this->assertEquals($expectedCarRelation, $relations['Car']['relations']);
        $this->assertEquals('Car', $relations['Car']['name']);
    }

    private function getBasicResult($filename)
    {
        $file = $this->getStubsFile($filename);
        $parser = new YamlParser($file);
        $entities = $parser->parse($file);
        $schema = new YamlSchema();
        foreach ($entities as $entity) {
            $schema->addEntity($entity);
        }
        $schema->build();
        return $schema;
    }
}