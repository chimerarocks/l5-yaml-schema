<?php
namespace ChimeraRocks\YamlSchema\Yaml\YamlConstraints;

abstract class YamlRelationConstraint implements IYamlConstraint
{
    protected $type;
    protected $entity;
    protected $table;
    protected $field;
    protected $references;

    const HAS_ONE = 'hasOne';
    const HAS_MANY = 'hasMany';
    const BELONGS_TO = 'belongsTo';
    const BELONGS_TO_MANY = 'belongsToMany';

    /**
     * YamlRelation constructor.
     * @param $type
     * @param $entity
     */
    public function __construct($attributes)
    {
        $this->type = $this->getType();
        if (is_array($attributes)) {
            $this->entity = is_array($attributes) ? key($attributes) : $attributes;
            isset($attributes[$this->entity]['field']) ?
                $this->setField(($attributes[$this->entity]['field'])) : null;
            isset($attributes[$this->entity]['table']) ?
                $this->setTable(($attributes[$this->entity]['table'])) : null;
            isset($attributes[$this->entity]['references']) ?
                $this->setReferences($attributes[$this->entity]['references']) : null;
        } else {
            $this->entity = $attributes;
        }
    }

    /**
     * @return mixed
     */
    abstract public function getType();

    abstract public function mustCreateForeignKey();

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->hasDinamicConstraint = true;
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField($field)
    {
        $this->hasDinamicConstraint = true;
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @param mixed $references
     */
    public function setReferences($references)
    {
        $this->hasDinamicConstraint = true;
        $this->references = $references;
    }

    public function parse()
    {
        if ($this->mustCreateForeignKey()) {
            $entityName = strtolower(last(explode('\\', $this->getEntity())));
            $field = $this->getField() ? $this->getField() : snake_case($entityName) . '_id';
            $table = $this->getTable() ? $this->getTable() : str_plural(snake_case($entityName));
            $references = $this->getReferences() ? $this->getReferences() : 'id';
            return "$field:foreign('$field')->references('$references')->on('$table')";
        }
    }

    public function getValidDinamicFields()
    {
        $fields = [];
        if ($this->table) {
            $fields['table'] = $this->table;
        }
        if ($this->field) {
            $fields['field'] = $this->field;
        }
        if ($this->references) {
            $fields['references'] = $this->references;
        }
        return $fields;
    }
}