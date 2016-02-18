<?php


namespace Anavel\Crud\Abstractor\Eloquent\Traits;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Illuminate\Http\Request;

trait HandleFiles
{
    protected function handleField(Request $request, $item, array $fields, $currentKey,  $groupName, $fieldName)
    {
        $modelFolder = $this->slug . DIRECTORY_SEPARATOR;
        $basePath = base_path(config('anavel-crud.uploads_path'));
        $modelPath = $basePath . $modelFolder;
        $skipNext = false;
        $requestValue = null;
        if (! empty($fields[$groupName][$currentKey + 1]) && $fields[$groupName][$currentKey + 1]->getName() === $fieldName . '__delete') {
            //We never want to save this field, it doesn't exist in the DB
            $skipNext = true;

            //If user wants to delete the existing file
            if (! empty($request->input("main.{$fieldName}__delete"))) {
                $adapter = new Local($basePath);
                $filesystem = new Filesystem($adapter);
                if ($filesystem->has($item->$fieldName)) {
                    $filesystem->delete($item->$fieldName);
                }


                $item->setAttribute(
                    $fieldName,
                    null
                );
                return [
                    'skipNext' => $skipNext
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
        } elseif (! $request->file($groupName .'.'.$fieldName)->isValid()) {
            throw new \Exception($request->file($groupName .'.'.$fieldName)->getErrorMessage());
        }

        return [
            'requestValue' => $requestValue,
            'skipNext' => $skipNext
        ];
    }
}