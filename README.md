# Crudoado
Crudoado automates the Create, Read, Update and Delete tasks over your Laravel application Eloquent models on a very fast and simple way.

> **Note:** This package is in active development and NOT ready for production.

### Features

* CRUD operations over Eloquent models.
* Search, pagination and sorting.

### Requirements.

* PHP 5.4 or higher.
* Laravel 5.
* [Adoadomin](https://github.com/ablunier/adoadomin).

## Instalation

To use Crudoado first you must install [Adoadomin](https://github.com/ablunier/adoadomin). Crudoado was concived as an Adoadomin module.

With Adoadomin installed and working, require this package with composer:

```
composer require ablunier/crudoado
```

After updating composer, add the ModuleProvider to the modules array in Adoadomin config:

```
ANavallaSuiza\Crudoado\CrudoadoModuleProvider::class
```

Copy the package config to your local config with the publish command:

```
php artisan vendor:publish
```

## Configuration

To start CRUDing your models just add them to the Crudoado config file as follows:

```
...
'models' => [
    'Users'      => 'App\User',
    'Blog Posts' => 'App\Post'
...
```

And that's all! TA-DÁ! You will find a full-featured CRUD on you admin panel.

## Documentation

Visit the [wiki](https://github.com/ablunier/crudoado/wiki) for more detailed information on how to customize your configuration based CRUD.

## License

This software is published under the MIT License
