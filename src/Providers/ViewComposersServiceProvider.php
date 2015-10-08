<?php
namespace ANavallaSuiza\Crudoado\Providers;

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
        $this->app->view->composer('crudoado::molecules.sidebar.default', 'ANavallaSuiza\Crudoado\View\Composers\SidebarComposer');
    }
}
