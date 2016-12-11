<?php
namespace Test\ChimeraRocks\YamlSchema\Generators;

use ChimeraRocks\YamlSchema\Generators\ModelGenerator;
use Tests\AbstractTestCase;

class ModelGeneratorTest extends AbstractTestCase
{
    public function getSimpleModelGenerator()
    {
        return new ModelGenerator([
            'name'   => 'User',
            'fields' => '',
            'force'  => '',
            'relations' => ''
        ]);
    }

    public function test_parser_relations_option_with_a_has_one()
    {
        $migration = $this->getSimpleModelGenerator();

        $parsed = $migration->parseRelationsOption('hasOne=entity:House');
        $relations = [
            'hasOne' => [
                [
                    'entity' => 'House',
                ]
            ]
        ];

        $this->assertEquals($parsed, $relations);
    }

    public function test_parser_relations_option_with_a_two_entities()
    {
        $migration = $this->getSimpleModelGenerator();

        $parsed = $migration->parseRelationsOption('hasOne=entity:House,field:home,references:numero;entity:Behavior,field:way');
        $relations = [
            'hasOne' => [
                [
                    'entity' => 'House',
                    'field' => 'home',
                    'references' => 'numero'
                ],
                [
                    'entity' => 'Behavior',
                    'field' => 'way',
                ]
            ],
        ];

        $this->assertEquals($parsed, $relations);
    }

    public function test_get_relation_with_a_has_one_relationship()
    {
        $migration = new ModelGenerator([
            'name'   => 'User',
            'fields' => '',
            'force'  => '',
            'relations' => 'hasOne=entity:House'
        ]);

        $relations = $migration->getRelations();

        $expected =
            "\tpublic function house()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->hasOne(House::class);" . PHP_EOL .
            "\t}";

        $this->assertEquals($expected, $relations);
    }

    public function test_get_relation_with_a_has_one_relationship_and_with_a_compoud_name()
    {
        $migration = new ModelGenerator([
            'name'   => 'User',
            'fields' => '',
            'force'  => '',
            'relations' => 'hasOne=entity:HouseCarl;entity:House,field:home_sweet_home'
        ]);

        $relations = $migration->getRelations();

        $expected =
            "\tpublic function houseCarl()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->hasOne(HouseCarl::class);" . PHP_EOL .
            "\t}" . PHP_EOL . PHP_EOL .
            "\tpublic function homeSweetHome()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->hasOne(House::class, 'home_sweet_home_id', 'id');" . PHP_EOL .
            "\t}"
        ;
        $this->assertEquals($expected, $relations);
    }

    public function test_get_relation_with_two_has_one_relationship_in_a_dinamic_way()
    {
        $migration = new ModelGenerator([
            'name'   => 'User',
            'fields' => '',
            'force'  => '',
            'relations' => 'hasOne=entity:House,field:home,references:numero;entity:Behavior,field:way'
        ]);

        $relations = $migration->getRelations();

        $expected =
            "\tpublic function home()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->hasOne(House::class, 'home_numero', 'numero');" . PHP_EOL .
            "\t}" . PHP_EOL . PHP_EOL .
            "\tpublic function way()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->hasOne(Behavior::class, 'way_id', 'id');" . PHP_EOL .
            "\t}"
        ;

        $this->assertEquals($expected, $relations);
    }

    public function test_get_relation_with_belongs_to_relationship()
    {
        $migration = new ModelGenerator([
            'name'   => 'User',
            'fields' => '',
            'force'  => '',
            'relations' => 'belongsTo=entity:Family'
        ]);

        $relations = $migration->getRelations();

        $expected =
            "\tpublic function family()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->belongsTo(Family::class);" . PHP_EOL .
            "\t}"
        ;

        $this->assertEquals($expected, $relations);
    }

    public function test_get_relation_with_has_many_relationship()
    {
        $migration = new ModelGenerator([
            'name'   => 'User',
            'fields' => '',
            'force'  => '',
            'relations' => 'hasMany=entity:Family'
        ]);

        $relations = $migration->getRelations();

        $expected =
            "\tpublic function families()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->hasMany(Family::class);" . PHP_EOL .
            "\t}"
        ;

        $this->assertEquals($expected, $relations);
    }

    public function test_get_relation_with_belongs_to_many_relationship()
    {
        $migration = new ModelGenerator([
            'name'   => 'User',
            'fields' => '',
            'force'  => '',
            'relations' => 'belongsToMany=entity:Family'
        ]);

        $relations = $migration->getRelations();

        $expected =
            "\tpublic function families()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->belongsToMany(Family::class);" . PHP_EOL .
            "\t}"
        ;

        $this->assertEquals($expected, $relations);
    }

    public function test_get_relation_with_belongs_to_many_relationship_dinamic()
    {
        $migration = new ModelGenerator([
            'name'   => 'User',
            'fields' => '',
            'force'  => '',
            'relations' => 'belongsToMany=entity:Family,joining_table:family_group,own:son_id,inversed:group_id'
        ]);

        $relations = $migration->getRelations();

        $expected =
            "\tpublic function families()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->belongsToMany(Family::class, 'family_group', 'son_id', 'group_id');" . PHP_EOL .
            "\t}"
        ;

        $this->assertEquals($expected, $relations);
    }

    public function test_get_relation_with_two_types_of_relationship()
    {
        $migration = new ModelGenerator([
            'name'   => 'User',
            'fields' => '',
            'force'  => '',
            'relations' => 'hasOne=entity:Car;entity:Job|belongsTo=entity:Family'
        ]);

        $relations = $migration->getRelations();

        $expected =
            "\tpublic function car()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->hasOne(Car::class);" . PHP_EOL .
            "\t}" . PHP_EOL . PHP_EOL .
            "\tpublic function job()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->hasOne(Job::class);" . PHP_EOL .
            "\t}" . PHP_EOL . PHP_EOL .
            "\tpublic function family()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->belongsTo(Family::class);" . PHP_EOL .
            "\t}"
        ;

        $this->assertEquals($expected, $relations);
    }
    
    public function test_generate_a_schema_without_relations()
    {
        $migration = new ModelGenerator([
            'name'   => 'Product',
            'fields' => '',
            'force'  => '',
            'fillable' => 'name:string(\'name\'),price:decimal(\'price\',6,2)',
            'relations' => ''
        ]);

        $relations = $migration->getRelations();

        $this->assertEmpty($relations);
    }
}