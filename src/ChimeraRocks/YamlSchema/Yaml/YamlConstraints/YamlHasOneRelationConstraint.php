<?php
namespace ChimeraRocks\YamlSchema\Yaml\YamlConstraints;

class YamlHasOneRelationConstraint extends YamlRelationConstraint
{
    public function getType()
    {
        return YamlRelationConstraint::HAS_ONE;
    }

    public function mustCreateForeignKey()
    {
        return false;
    }

    public function isRelationConstraint()
    {
        return true;
    }
}