<?php
namespace ChimeraRocks\YamlSchema\Yaml;

class YamlFieldFactory
{
    public function make(YamlEntity &$entity, $attributes, $name = '')
    {
        $type = array_pull($attributes, 'type');
        $length = array_pull($attributes, 'length');
        $field = new YamlField($name, $type, $length);
        if (count($attributes) > 0) {
            $field->setOptions($attributes);
        }
        $entity->addField($field);
    }
}