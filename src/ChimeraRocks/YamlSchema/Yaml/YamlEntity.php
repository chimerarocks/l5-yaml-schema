<?php
namespace ChimeraRocks\YamlSchema\Yaml;

use ChimeraRocks\YamlSchema\Yaml\YamlConstraints\IYamlConstraint;
use Illuminate\Database\Eloquent\Collection;

class YamlEntity
{
    private $name;

    private $fields;

    private $constraints;

    public function __construct($name)
    {
        $this->name = $name;
        $this->fields = new Collection();
        $this->constraints = new Collection();
    }

    public function getConstraints()
    {
        return $this->constraints;
    }

    public function addConstraint(IYamlConstraint $constraint)
    {
        $this->constraints->add($constraint);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function addField(YamlField $field)
    {
        $this->fields->add($field);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}