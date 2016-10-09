<?php

namespace Rorikurn\LaravelActivation;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class ActivatorServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    private $namespace = 'activator';

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $aliases = [];

    /**
     * Register the service provider
     */
    public function register()
    {
        $this->registerMiddleware();
        $this->registerServiceProviders();
        $this->registerAliases();
        $this->registerConfig();
        $this->registerMigrations();
    }

    /**
     * Load the resources
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', $this->namespace);
    }

    /**
     * @return void
     */
    private function registerMiddleware()
    {
    }

    /**
     * @return void
     */
    private function registerServiceProviders()
    {
        foreach ($this->providers as $provider)
        {
            $this->app->register($provider);
        }
    }

    /**
     * @return void
     */
    private function registerAliases()
    {
        $loader = AliasLoader::getInstance();
        foreach ($this->aliases as $key => $alias)
        {
            $loader->alias($key, $alias);
        }
    }

    /**
     * @return void
     */
    private function registerConfig()
    {
        $configPath = __DIR__ . '/../config/activator.php';

        $this->mergeConfigFrom($configPath, 'activator');
    }

    /**
     * Register migration files.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'activator-migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}