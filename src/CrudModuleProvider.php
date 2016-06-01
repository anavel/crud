<?php
namespace Anavel\Crud;

use Anavel\Foundation\Support\ModuleProvider;
use Anavel\Crud\Abstractor\Eloquent\ModelFactory as ModelAbstractorFactory;
use Anavel\Crud\Abstractor\Eloquent\RelationFactory as RelationAbstractorFactory;
use Anavel\Crud\Abstractor\Eloquent\FieldFactory as FieldAbstractorFactory;
use Anavel\Crud\Http\Form\Generator as FormGenerator;
use FormManager\Factory as FormFactory;
use Request;
use Schema;

class CrudModuleProvider extends ModuleProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'anavel-crud');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'anavel-crud');

        $this->publishes([
            __DIR__.'/../public/' => public_path('vendor/anavel-crud/'),
        ], 'assets');

        $this->publishes([
            __DIR__.'/../config/anavel-crud.php' => config_path('anavel-crud.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/anavel-crud.php', 'anavel-crud');

        $this->app->register('ANavallaSuiza\Laravel\Database\Manager\ModelManagerServiceProvider');

        $this->app->bind(
            'Anavel\Crud\Contracts\Abstractor\FieldFactory',
            function () {
                return new FieldAbstractorFactory(new FormFactory);
            }
        );

        $this->app->bind(
            'Anavel\Crud\Contracts\Abstractor\RelationFactory',
            function () {
                return new RelationAbstractorFactory(
                    $this->app['ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'],
                    $this->app['Anavel\Crud\Contracts\Abstractor\FieldFactory']
                );
            }
        );

        $this->app->bind(
            'Anavel\Crud\Contracts\Abstractor\ModelFactory',
            function () {
                return new ModelAbstractorFactory(
                    config('anavel-crud.models'),
                    $this->app['ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'],
                    $this->app['Anavel\Crud\Contracts\Abstractor\RelationFactory'],
                    $this->app['Anavel\Crud\Contracts\Abstractor\FieldFactory'],
                    $this->app['Anavel\Crud\Contracts\Form\Generator']
                );
            }
        );

        $this->app->bind(
            'Anavel\Crud\Contracts\Form\Generator',
            function () {
                return new FormGenerator(new FormFactory);
            }
        );

        $this->app->register('Anavel\Crud\Providers\ViewComposersServiceProvider');

        $this->registerDoctrineTypeMappings();
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
        return config('anavel-crud.name');
    }

    public function routes()
    {
        return __DIR__.'/Http/routes.php';
    }

    public function mainRoute()
    {
        return route('anavel-crud.home');
    }

    public function hasSidebar()
    {
        return true;
    }

    public function sidebarMenu()
    {
        return 'anavel-crud::molecules.sidebar.default';
    }

    public function isActive()
    {
        $uri = Request::route()->uri();

        if (strpos($uri, 'crud') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Registers types that Doctrine doesn's support by default
     */
    protected function registerDoctrineTypeMappings()
    {
        $connection = Schema::getConnection();
        $platform = $connection->getDoctrineConnection()->getDatabasePlatform();

        $platform->registerDoctrineTypeMapping('enum', 'string');
    }
}
