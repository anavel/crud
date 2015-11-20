<?php
namespace ANavallaSuiza\Crudoado;

use ANavallaSuiza\Adoadomin\Support\ModuleProvider;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\ModelFactory as ModelAbstractorFactory;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\RelationFactory as RelationAbstractorFactory;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\FieldFactory as FieldAbstractorFactory;
use ANavallaSuiza\Crudoado\Http\Form\Generator as FormGenerator;
use FormManager\Factory as FormFactory;
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

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'crudoado');

        $this->publishes([
            __DIR__.'/../public/js' => public_path('vendor/crudoado/js'),
        ], 'assets');

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

        $this->app->register('ANavallaSuiza\Laravel\Database\Manager\ModelManagerServiceProvider');

        $this->app->bind(
            'ANavallaSuiza\Crudoado\Contracts\Abstractor\FieldFactory',
            function () {
                return new FieldAbstractorFactory(new FormFactory);
            }
        );

        $this->app->bind(
            'ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory',
            function () {
                return new RelationAbstractorFactory(
                    $this->app['ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager']
                );
            }
        );

        $this->app->bind(
            'ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory',
            function () {
                return new ModelAbstractorFactory(
                    config('crudoado.models'),
                    $this->app['ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'],
                    $this->app['ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory'],
                    $this->app['ANavallaSuiza\Crudoado\Contracts\Form\Generator']
                );
            }
        );

        $this->app->bind(
            'ANavallaSuiza\Crudoado\Contracts\Form\Generator',
            function () {
                return new FormGenerator(new FormFactory);
            }
        );

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
