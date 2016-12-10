<?php

namespace ChimeraRocks\YamlSchema\Providers;

use Illuminate\Support\ServiceProvider;

class YamlSchemaServiceProvider extends ServiceProvider
{
	protected $defer = false;

	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../../../resources/config/repository.php' => config_path('repository.php')
		]);

		$this->publishes([
			__DIR__ . '/../../../resources/Stubs' => base_path('resources/repository/Stubs')
		]);

		$this->mergeConfigFrom(__DIR__ . '/../../../resources/config/repository.php', 'repository');

		$this->loadTranslationsFrom(__DIR__ . '/../../../resources/lang', 'repository');
	}

	/**
     * Register the service provider.
     *
     * @return void
     */
	public function register()
	{
		$this->commands('ChimeraRocks\YamlSchema\Commands\SchemaCommand');
		$this->commands('ChimeraRocks\YamlSchema\Commands\RepositoryCommand');
		$this->commands('Prettus\Repository\Generators\Commands\TransformerCommand');
		$this->commands('Prettus\Repository\Generators\Commands\PresenterCommand');
		$this->commands('ChimeraRocks\YamlSchema\Commands\EntityCommand');
		$this->commands('Prettus\Repository\Generators\Commands\ValidatorCommand');
		$this->commands('Prettus\Repository\Generators\Commands\ControllerCommand');
		$this->commands('Prettus\Repository\Generators\Commands\BindingsCommand');
		$this->commands('Prettus\Repository\Generators\Commands\CriteriaCommand');
		$this->app->register('Prettus\Repository\Providers\EventServiceProvider');
	}
}