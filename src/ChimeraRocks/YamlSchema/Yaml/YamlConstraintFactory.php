<?php
namespace ChimeraRocks\YamlSchema\Yaml;

use ChimeraRocks\YamlSchema\Yaml\YamlConstraints\YamlBelongsToManyRelationConstraint;
use ChimeraRocks\YamlSchema\Yaml\YamlConstraints\YamlBelongsToRelationConstraint;
use ChimeraRocks\YamlSchema\Yaml\YamlConstraints\YamlHasManyRelationConstraint;
use ChimeraRocks\YamlSchema\Yaml\YamlConstraints\YamlHasOneRelationConstraint;

class YamlConstraintFactory
{
    public function make(YamlEntity &$entity, $attributes, $constraint)
    {
        switch ($constraint) {
            case 'hasOne': return $entity->addConstraint(new YamlHasOneRelationConstraint($attributes));
            case 'hasMany': return $entity->addConstraint(new YamlHasManyRelationConstraint($attributes));
            case 'belongsTo': return $entity->addConstraint(new YamlBelongsToRelationConstraint($attributes));
            case 'belongsToMany': return $entity->addConstraint(new YamlBelongsToManyRelationConstraint($attributes));
        }
        return null;
    }
}