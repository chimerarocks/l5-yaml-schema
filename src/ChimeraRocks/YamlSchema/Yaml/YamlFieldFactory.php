<?php
namespace ChimeraRocks\YamlSchema\Yaml;

class YamlFieldFactory
{
    public function make(YamlEntity &$entity, $attributes, $name = '')
    {
        if (is_string($attributes)) {
            $type = $attributes;
            $attributes = null;
            $length = null;
            $constraints = null;
        } else {
            $type = array_pull($attributes, 'type');
            $length = array_pull($attributes, 'length');
            $constraints = array_pull($attributes, 'constraints');
        }
        $field = new YamlField($name, $type, $length);

        if (!empty($constraints)) {
            $field->setConstraints($constraints);
        }

        if (count($attributes) > 0) {
            $field->setOptions($attributes);
        }
        $entity->addField($field);
    }
}