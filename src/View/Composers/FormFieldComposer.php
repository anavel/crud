<?php


namespace Anavel\Crud\View\Composers;

use Anavel\Foundation\Contracts\Anavel;

class FormFieldComposer
{
    /**
     * @var Anavel
     */
    protected $anavel;

    public function __construct(Anavel $anavel)
    {
        $this->anavel = $anavel;
    }

    public function compose($view)
    {
        $uploadsModuleIsInstalled = $this->anavel->hasModule('Anavel\Uploads\UploadsModuleProvider');

        $view->with([
            'canTakeFileFromUploads' => $uploadsModuleIsInstalled
        ]);
    }
}