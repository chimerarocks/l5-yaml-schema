<?php

namespace ChimeraRocks\YamlSchema\Generators;

use Prettus\Repository\Generators\Generator;

class RequestGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'request';

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
        return 'request';
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
            'rules' => $this->getRules()
        ]);
    }


    /**
     * Get schema parser.
     *
     * @return SchemaParser
     */
    public function getSchemaParser()
    {
        return new SchemaParser($this->fields);
    }


    /**
     * Get name parser.
     *
     * @return NameParser
     */
    public function getNameParser()
    {
        return new NameParser($this->name);
    }


    /**
     * Get stub templates.
     *
     * @return string
     */
    public function getStub()
    {
        $parser = $this->getNameParser();

        $action = $parser->getAction();
        switch ($action) {
            case 'add':
            case 'append':
            case 'update':
            case 'insert':
                $file = 'change';
                $replacements = [
                    'class'       => $this->getClass(),
                    'table'       => $parser->getTable(),
                    'fields_up'   => $this->getSchemaParser()->up(),
                    'fields_down' => $this->getSchemaParser()->down(),
                ];
                break;

            case 'delete':
            case 'remove':
            case 'alter':
                $file = 'change';
                $replacements = [
                    'class'       => $this->getClass(),
                    'table'       => $parser->getTable(),
                    'fields_down' => $this->getSchemaParser()->up(),
                    'fields_up'   => $this->getSchemaParser()->down(),
                ];
                break;
            default:
                $file = 'create';
                $replacements = [
                    'class'  => $this->getClass(),
                    'table'  => $parser->getTable(),
                    'fields' => $this->getSchemaParser()->up(),
                ];
                break;
        }
        $path = config('repository.generator.stubsOverridePath', __DIR__);

        if (!file_exists($path . "/Stubs/migration/{$file}.stub")) {
            $path = __DIR__;
        }

        if (!file_exists($path . "/Stubs/migration/{$file}.stub")) {
            throw new FileNotFoundException($path . "/Stubs/migration/{$file}.stub");
        }

        return Stub::create($path . "/Stubs/migration/{$file}.stub", $replacements);
    }
}