<?php

namespace ChimeraRocks\YamlSchema\Generators;

use Prettus\Repository\Generators\Generator;
use ChimeraRocks\YamlSchema\Generators\SchemaParser;

/**
 * Class ModelGenerator
 * @package Prettus\Repository\Generators
 */
class ModelGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'model';

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return parent::getRootNamespace() . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'models';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . '.php';
    }

    /**
     * Get base path of destination file.
     *
     * @return string
     */

    public function getBasePath()
    {
        return config('repository.generator.basePath', app_path());
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        return array_merge(parent::getReplacements(), [
            'fillable' => $this->getFillable(),
            'relations' => $this->getRelations()
        ]);
    }

    /**
     * Get the fillable attributes.
     *
     * @return string
     */
    public function getFillable()
    {
        if (!$this->fillable) {
            return '[]';
        }
        $results = '[' . PHP_EOL;

        foreach ($this->getSchemaParser()->toArray() as $column => $value) {
            $results .= "\t\t'{$column}'," . PHP_EOL;
        }

        return $results . "\t" . ']';
    }


    /**
     * Get the relations methods.
     *
     * @return string
     */
    public function getRelations()
    {
        $opt = $this->options['relations'];
        if (is_string($opt)) {
            $relations = $this->parseRelationsOption($opt);
        } else if (is_array($opt)){
            $relations = $opt;
        } 
        $result = '';
        foreach ($relations as $relation => $entities) {
            if ($this->isValidSingularType($relation)) {
                $result .= $this->getRelationsMethod($relation, $entities);
            } else if ($this->isValidPluralType($relation)) {
                $result .= $this->getRelationsMethod($relation, $entities, true);
            }
        }

        return rtrim($result, PHP_EOL);
    }

    public function parseRelationsOption($option)
    {
        $relationsArr = [];
        foreach (explode('|', $option) as $relation) {
            $opt = explode('=', $relation);
            $relationsType = $opt[0];
            $entityArr = [];
            foreach (explode(';', $opt[1]) as $entities) {
                $fieldArr = [];
                foreach(explode(',', $entities) as $fields) {
                    $field = explode(':', $fields);
                    $fieldName = $field[0];
                    $fieldValue = $field[1];
                    $fieldArr[$fieldName] = $fieldValue;
                }
                $entityArr[] = $fieldArr;
            }
            $relationsArr[$relationsType] = $entityArr;
        }
        return $relationsArr;
    }

    public function isValidSingularType($type)
    {
        $valid_types = [
            'hasOne',
            'belongsTo'
        ];
        return in_array($type, $valid_types);
    }

    public function isValidPluralType($type)
    {
        $valid_types = [
            'hasMany',
            'belongsToMany'
        ];
        return in_array($type, $valid_types);
    }

    public function getRelationsMethod($type, $entities, $plural = false)
    {
        $method = '';
        foreach ($entities as $entity) {
            $name = $this->getMethodName($entity, $plural);
            $method .= $this->getMethod($type, $name, $entity);
        }

        return $method;
    }

    public function getMethodName($entity, $plural = false)
    {
        if ($plural) {
            return isset($entity['field']) ? str_plural(camel_case($entity['field'])) : str_plural(camel_case($entity['entity']));
        }
        return isset($entity['field']) ? camel_case($entity['field']) : camel_case($entity['entity']);
    }

    public function getMethod($type, $name, $entity)
    {
        $method = '';
        $entityName = $entity['entity'];

        $method .=
            "\tpublic function $name()" . PHP_EOL .
            "\t{" . PHP_EOL .
            "\t\treturn \$this->$type($entityName::class" . $this->dinamicFields($entity) . ");" . PHP_EOL .
            "\t}" . PHP_EOL . PHP_EOL;

        return $method;
    }

    public function dinamicFields($entity)
    {
        $field = '';
        if (isset($entity['joining_table'])) {
            $field = ', \'' . $entity['joining_table']. '\'';
            if (isset($entity['own'])) {
                $field .= ', \'' . $entity['own']. '\'';
                if (isset($entity['inversed'])) {
                    $field .= ', \'' . $entity['inversed']. '\'';
                }
            }

            return $field;
        }

        if (isset($entity['field'])) {
            $field = ', \'' . $entity['field'];
            if (isset($entity['references'])) {
                $references = $entity['references'];
                $field .= '_' . $references . '\', \'' . $references . '\'';
            } else {
                $field .= '_id\', \'id\'';
            }
        }
        return $field;
    }

    /**
     * Get schema parser.
     *
     * @return SchemaParser
     */
    public function getSchemaParser()
    {
        return new SchemaParser($this->fillable);
    }
}
