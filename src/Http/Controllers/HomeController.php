<?php
namespace Anavel\Crud\Http\Controllers;

use Anavel\Foundation\Http\Controllers\Controller;
use EasySlugger\Slugger;
use Illuminate\Http\RedirectResponse;
use Anavel\Crud\Contracts\Abstractor\ModelFactory as ModelAbstractorFactory;
use Gate;

class HomeController extends Controller
{
    public function index(ModelAbstractorFactory $modelFactory)
    {
        $models = config('anavel-crud.models');

        if (empty($models)) {
            throw new \Exception("No models configured.");
        }


        foreach ($models as $modelName => $model) {
            $modelSlug = Slugger::slugify($modelName);
            $modelAbstractor = $modelFactory->getByName($modelSlug);
            $config = $modelAbstractor->getConfig();

            if (! array_key_exists('authorize', $config) || ( $config['authorize'] === true && Gate::allows('adminIndex', $modelAbstractor->getInstance()) ||  $config['authorize'] === false )) {
                return new RedirectResponse(route('anavel-crud.model.index', $modelSlug));
            }

        }

        abort(403);
    }
}