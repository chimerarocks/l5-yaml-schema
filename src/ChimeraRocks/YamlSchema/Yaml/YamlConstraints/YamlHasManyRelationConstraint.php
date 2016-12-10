<?php
namespace ChimeraRocks\YamlSchema\Yaml\YamlConstraints;

class YamlHasManyRelationConstraint extends YamlRelationConstraint
{
    public function getType()
    {
        return YamlRelationConstraint::HAS_MANY;
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