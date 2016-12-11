<?php
namespace ChimeraRocks\YamlSchema\Yaml;

use ChimeraRocks\YamlSchema\Exceptions\InversedEntityNotExistsException;
use ChimeraRocks\YamlSchema\Yaml\YamlConstraints\YamlBelongsToRelationConstraint;
use ChimeraRocks\YamlSchema\Yaml\YamlConstraints\YamlRelationConstraint;
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
                $this->checkInversedEntityExists($entity, $relation);
                $rel = $this->buildRelation($relation);
                $options .= $relation->parse();


                $type = $relation->getType();
                $relationArr[$type][] = $rel;
            }

            if ($entity->getName() == 'User') {
                $result = $this->getEntityWhichHasThis($entity);
                if (!empty($result)) {
                    $already = $this->thisRelationsHasBelongsToFieldToEntity($relations, $result);
                    if (empty($already)) {
                        $belongsTo = new YamlBelongsToRelationConstraint($result->getName());
                        $options .= $belongsTo->parse();
                    }
                }
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

    public function checkInversedEntityExists($entity, $relation)
    {
        $hasInversedEntity = $this->getEntities()->first(function ($entity, $key) use ($relation) {
            return $entity->getName() == $relation->getEntity();
        });
        if (empty($hasInversedEntity)) {
            throw new InversedEntityNotExistsException($entity->getName(), $relation->getEntity());
        }
    }

    public function buildRelation($relation)
    {
        $rel = $relation->getValidDinamicFields();
        $rel['entity'] = $relation->getEntity();
        return $rel;
    }

    public function getEntityWhichHasThis($entity)
    {
        $result = $this->getEntities()->first(function ($object, $key) use ($entity) {
            return $object->getConstraints()->first(function ($constraint, $key) use ($entity) {
                return in_array($constraint->getType(), $this->getHasFieldsThatNeedsInversedBelongsTo()) &&
                $constraint->getEntity() == $entity->getName();
            });
        });
        return $result;
    }

    public function thisRelationsHasBelongsToFieldToEntity($relations, $entity)
    {
        return $relations->first(function($object, $key) use ($entity) {
            return $object->getEntity() == $entity->getName();
        });
    }

    public function getHasFieldsThatNeedsInversedBelongsTo()
    {
        return [
            YamlRelationConstraint::HAS_MANY,
            YamlRelationConstraint::HAS_ONE
        ];
    }
}