<?php


namespace Anavel\Crud\Abstractor\Eloquent\Traits;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Illuminate\Http\Request;

trait HandleFiles
{
    protected function handleField(Request $request, $item, array $fields, $groupName, $fieldName)
    {
        $modelFolder = $this->slug . DIRECTORY_SEPARATOR;
        $basePath = base_path(config('anavel-crud.uploads_path'));
        $modelPath = $basePath . $modelFolder;
        $skip = null;
        $requestValue = null;
        if (! empty($fields["{$fieldName}__delete"])) {
            //We never want to save this field, it doesn't exist in the DB
            $skip = "{$fieldName}__delete";


            //If user wants to delete the existing file
            if (! empty($request->input("{$groupName}.{$fieldName}__delete"))) {
                $adapter = new Local($basePath);
                $filesystem = new Filesystem($adapter);
                dd($item);
                if ($filesystem->has($item->$fieldName)) {
                    $filesystem->delete($item->$fieldName);
                }


                $item->setAttribute(
                    $fieldName,
                    null
                );
                return [
                    'skip' => $skip
                ];
            }
        }
        if ($request->hasFile($groupName .'.'.$fieldName)) {
            $fileName = uniqid() . '.' . $request->file($groupName .'.'.$fieldName)->getClientOriginalExtension();


            $request->file($groupName .'.'.$fieldName)->move(
                $modelPath,
                $fileName
            );

            $requestValue = $modelFolder . $fileName;
        } elseif (! empty($request->file($groupName .'.'.$fieldName)) && ! $request->file($groupName .'.'.$fieldName)->isValid()) {
            throw new \Exception($request->file($groupName .'.'.$fieldName)->getErrorMessage());
        }

        return [
            'requestValue' => $requestValue,
            'skip' => $skip
        ];
    }
}