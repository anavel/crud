<?php
namespace ANavallaSuiza\Crudoado;

use ANavallaSuiza\Adoadomin\Support\ModuleProvider;
use Request;

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

        $this->app->register('ANavallaSuiza\Crudoado\Providers\ViewComposersServiceProvider');
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

    public function routes()
    {
        return __DIR__.'/Http/routes.php';
    }

    public function mainRoute()
    {
        return route('crudoado.home');
    }

    public function hasSidebar()
    {
        return true;
    }

    public function sidebarMenu()
    {
        return 'crudoado::molecules.sidebar.default';
    }

    public function isActive()
    {
        $uri = Request::route()->uri();

        if (strpos($uri, 'crudoado') !== false) {
            return true;
        }

        return false;
    }
}
