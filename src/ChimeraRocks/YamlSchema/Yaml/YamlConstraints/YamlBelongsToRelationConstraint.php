<?php
namespace ChimeraRocks\YamlSchema\Yaml\YamlConstraints;

class YamlBelongsToRelationConstraint extends YamlRelationConstraint
{
    public function getType()
    {
        return YamlRelationConstraint::BELONGS_TO;
    }

    public function mustCreateForeignKey()
    {
        return true;
    }

    public function isRelationConstraint()
    {
        return true;
    }
}