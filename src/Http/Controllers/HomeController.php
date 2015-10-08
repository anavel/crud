<?php
namespace ANavallaSuiza\Crudoado\Http\Controllers;

use ANavallaSuiza\Adoadomin\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use EasySlugger\Slugger;

class HomeController extends Controller
{
    public function index()
    {
        $models = config('crudoado.models');

        if (empty($models)) {
            throw new \Exception("No models configured.");
        }

        $modelSlug = Slugger::slugify(key($models));

        return new RedirectResponse(route('crudoado.model.index', $modelSlug));
    }
}