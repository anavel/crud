<?php
namespace ANavallaSuiza\Crudoado;

use ANavallaSuiza\Adoadomin\Support\ModuleProvider;

class CrudoadoModuleProvider extends ModuleProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        include __DIR__.'/Http/routes.php';

        $this->loadViewsFrom(__DIR__.'/../views', 'crudoado');

        //$this->loadTranslationsFrom(__DIR__.'/../lang', 'crudoado');

        $this->publishes([
            __DIR__.'/../config/crudoado.php' => config_path('crudoado.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/crudoado.php', 'crudoado');
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

    public function name()
    {
        return config('crudoado.name');
    }

    public function mainRoute()
    {
        return '';
    }

    public function hasSidebar()
    {
        return true;
    }

    public function sidebarItems()
    {
        return [];
    }

    public function isActive()
    {
        return true;
    }
}
