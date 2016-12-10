<?php
namespace ChimeraRocks\YamlSchema\Commands;

use ChimeraRocks\YamlSchema\Yaml\YamlParser;
use ChimeraRocks\YamlSchema\Yaml\YamlSchema;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SchemaCommand extends Command
{
    protected $name = 'schema:build';

    protected $description = 'Build the schema';

    public function fire()
    {
        $path = config('repository.generator.schemaPath');
        $pattern = join(DIRECTORY_SEPARATOR, [$path, '*.yml']);
        $pattern2 = join(DIRECTORY_SEPARATOR, [$path, '*.yaml']);

        $parser = new YamlParser();
        $schema = new YamlSchema();

        foreach (glob($pattern) as $filename) {
            $entities = $parser->parse($filename);
            foreach ($entities as $entity) {
                $schema->addEntity($entity);
            }
        }
        foreach (glob($pattern2) as $filename) {
            $entities = $parser->parse($filename);
            foreach ($entities as $entity) {
                $schema->addEntity($entity);
            }
        }

        $schema->build();

        $migrations = $schema->getMigrations();
        $relations = $schema->getRelations();

        if ($this->confirm('Would you like Presenters? [y|N]')) {
            foreach ($migrations as $migration) {
                $this->call('make:presenter', [
                    'name'    => $migration['name'],
                    '--force' => $this->option('force'),
                ]);
            }
        }

        if ($this->confirm('Would you like Controllers? [y|N]')) {
            foreach ($migrations as $migration) {
                // Generate a controller resource
                $this->call('make:resource', [
                    'name'    => $migration['name'],
                    '--force' => $this->option('force')
                ]);
            }
        }

        foreach ($migrations as $entity => $migration) {
            $this->call('make:repository', [
                'name'        => $migration['name'],
                '--fillable'  => $migration['options'],
                '--relations'  => $relations[$entity]['relations'],
                '--force'     => $this->option('force')
            ]);
        }

        foreach ($migrations as $migration) {
            $this->call('make:bindings', [
                'name'    => $migration['name'],
                '--force' => $this->option('force')
            ]);
        }
    }

    /**
     * The array of command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            [
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force the creation if file already exists.',
                null
            ]
        ];
    }
}