# Crudoado [![Build Status](https://travis-ci.org/ablunier/crudoado.svg)](https://travis-ci.org/ablunier/crudoado)
Crudoado automates the Create, Read, Update and Delete tasks over your Laravel application Eloquent models on a very fast and simple way.

> **Note:** This package is in active development and NOT ready for production.

### Features

* CRUD operations over Eloquent models.
* Search, pagination and sorting.

### Requirements.

* PHP 5.4 or higher.
* Laravel 5.
* [Adoadomin](https://github.com/anavallasuiza/adoadomin).

## Instalation

To use Crudoado you must first install [Adoadomin](https://github.com/anavallasuiza/adoadomin). Crudoado was conceived as an Adoadomin module.

With Adoadomin installed and working, require this package with composer:

```
composer require anavallasuiza/crudoado
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
    'Users'      => App\User::class,
    'Blog Posts' => App\Post::class
...
```

And that's all! TA-DÁ! You will find a full-featured CRUD on you admin panel.

## Documentation

Visit the [wiki](https://github.com/anavallasuiza/crudoado/wiki) for more detailed information on how to customize your configuration based CRUD.

## License

This software is published under the MIT License
