<?php
namespace ChimeraRocks\YamlSchema\Yaml\YamlConstraints;

class YamlBelongsToManyRelationConstraint extends YamlRelationConstraint
{
    private $own;

    private $inversed;

    private $joining_table;

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        if (is_array($attributes)) {
            isset($attributes[$this->entity]['own']) ?
                $this->setOwn(($attributes[$this->entity]['own'])) : null;
            isset($attributes[$this->entity]['inversed']) ?
                $this->setInversed(($attributes[$this->entity]['inversed'])) : null;
            isset($attributes[$this->entity]['joining_table']) ?
                $this->setJoiningTable($attributes[$this->entity]['joining_table']) : null;
        }

    }

    /**
     * @return mixed
     */
    public function getOwn()
    {
        return $this->own;
    }

    /**
     * @param mixed $own
     */
    public function setOwn($own)
    {
        $this->own = $own;
    }

    /**
     * @return mixed
     */
    public function getInversed()
    {
        return $this->inversed;
    }

    /**
     * @param mixed $inversed
     */
    public function setInversed($inversed)
    {
        $this->inversed = $inversed;
    }

    /**
     * @return mixed
     */
    public function getJoiningTable()
    {
        return $this->joining_table;
    }

    /**
     * @param mixed $joining_table
     */
    public function setJoiningTable($joining_table)
    {
        $this->joining_table = $joining_table;
    }

    public function isRelationConstraint()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return YamlRelationConstraint::BELONGS_TO_MANY;
    }

    public function mustCreateForeignKey()
    {
        return false;
    }

    public function getValidDinamicFields()
    {
        $fields = [];
        if ($this->own) {
            $fields['own'] = $this->own;
        }
        if ($this->inversed) {
            $fields['inversed'] = $this->inversed;
        }
        if ($this->joining_table) {
            $fields['joining_table'] = $this->joining_table;
        }
        return $fields;
    }
}