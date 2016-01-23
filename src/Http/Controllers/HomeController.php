<?php
namespace Anavel\Crud\Http\Controllers;

use Anavel\Foundation\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use EasySlugger\Slugger;

class HomeController extends Controller
{
    public function index()
    {
        $models = config('anavel-crud.models');

        if (empty($models)) {
            throw new \Exception("No models configured.");
        }

        $modelSlug = Slugger::slugify(key($models));

        return new RedirectResponse(route('anavel-crud.model.index', $modelSlug));
    }
}