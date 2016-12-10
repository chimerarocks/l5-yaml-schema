<?php
namespace ChimeraRocks\YamlSchema\Yaml;

use Illuminate\Database\Eloquent\Collection;

class YamlSchema
{
    private $entities;

    private $migrations;

    private $relations;

    public function __construct(array $migrations = [], array $relations = [])
    {
        $this->migrations = $migrations;
        $this->relations = $relations;
        $this->entities = new Collection();
    }

    /**
     * @return Collection
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param YamlEntity $entity
     */
    public function addEntity(YamlEntity $entity)
    {
        $this->entities->add($entity);
    }

    /**
     * @return array
     */
    public function getMigrations()
    {
        return $this->migrations;
    }

    /**
     * @param array $migrations
     */
    public function addMigration($class, $migration)
    {
        $this->migrations[$class] = $migration;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param array $relations
     */
    public function addRelation($class, $model)
    {
        $this->relations[$class] = $model;
    }

    public function getCallToMigration($class)
    {
        return $this->migrations[$class];
    }

    public function getRelationsToRelationOrNull($class)
    {
        return isset($this->migrations[$class]) ? $this->migrations[$class] : null;
    }

    public function build()
    {
        $entities = $this->getEntities();
        foreach ($entities as $entity) {
            $fields = $entity->getFields();
            $options = '';
            foreach ($fields as $field) {
                $options .= $field->parse();
                $options .= ',';
            }

            $relations = $entity->getConstraints();
            $relationArr = [];
            foreach ($relations as $relation) {
                $options .= $relation->parse();
                $rel = $relation->getValidDinamicFields();
                $rel['entity'] = $relation->getEntity();
                $type = $relation->getType();
                $relationArr[$type][] = $rel;
            }
            
            $migration = [
                "name" => $entity->getName(),
                "options" => rtrim($options, ',')
            ];
            $this->addMigration($entity->getName(), $migration);

            if (!empty($relationArr)) {
                $relation = [
                    "name" => $entity->getName(),
                    "relations" => $relationArr
                ];
                $this->addRelation($entity->getName(), $relation);
            }
        }
    }
}