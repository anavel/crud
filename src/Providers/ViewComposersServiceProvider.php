<?php
namespace Anavel\Crud\Providers;

use Illuminate\Support\ServiceProvider;

class ViewComposersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->view->composer('anavel-crud::molecules.sidebar.default', 'Anavel\Crud\View\Composers\SidebarComposer');
    }
}
