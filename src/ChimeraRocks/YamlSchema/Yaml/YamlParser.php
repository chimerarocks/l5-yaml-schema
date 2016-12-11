<?php
namespace ChimeraRocks\YamlSchema\Yaml;

use ChimeraRocks\YamlSchema\Exceptions\InversedEntityNotExistsException;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Yaml\Yaml;

class YamlParser
{
    private $file;
    private $yamlFieldFactory;
    private $yamlConstraintFactory;

    public function __construct($file = null)
    {
        $this->file = $file;
        $this->yamlFieldFactory = new YamlFieldFactory();
        $this->yamlConstraintFactory = new YamlConstraintFactory();
    }

    public function parse($file = null)
    {
        if ($file) {
            $this->file = $file;
        }
        $fileContents = Yaml::parse(file_get_contents($this->file));
        $entities = new Collection();
        foreach ($fileContents as $entity => $array) {
            $yamlEntity = new YamlEntity($entity);
            $fields = array_pull($array, 'fields');
            foreach ($fields as $column => $attributes) {
                $this->yamlFieldFactory->make($yamlEntity, $attributes, $column);
            }
            foreach($array as $constraint => $attributes) {
                if (is_array($attributes['entity'])) {
                    foreach ($attributes['entity'] as $entityRel => $constraintAttr) {
                        if (is_numeric($entityRel) && is_string($constraintAttr)) {
                            $this->yamlConstraintFactory->make($yamlEntity, $constraintAttr, $constraint);
                        } else {
                            $data = [$entityRel => $constraintAttr];
                            $this->yamlConstraintFactory->make($yamlEntity, $data, $constraint);
                        }
                    }
                } else {
                    $this->yamlConstraintFactory->make($yamlEntity, $attributes['entity'], $constraint);
                }
            }
            $entities->add($yamlEntity);
        }

        return $entities;
    }
}