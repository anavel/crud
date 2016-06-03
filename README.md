# Anavel CRUD [![Build Status](https://travis-ci.org/anavel/crud.svg)](https://travis-ci.org/anavel/crud) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/anavel/crud/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/anavel/crud/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/anavel/crud/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/anavel/crud/?branch=master)
Anavel CRUD automates the Create, Read, Update and Delete tasks over your Laravel application Eloquent models on a very fast and simple way.

> **Note:** This package is in active development and NOT ready for production.

### Features

* CRUD operations over Eloquent models.
* Search, pagination and sorting.

### Requirements.

* PHP 5.4 or higher.
* Laravel 5.
* [Anavel foundation](https://github.com/anavel/foundation).

## Instalation

To use this package you must first install [Anavel foundation](https://github.com/anavel/foundation). This package was conceived as an Anavel module.

With Anavel installed and working, require this package with composer:

```
composer require anavel/crud
```

After updating composer, add the ModuleProvider to the modules array in anavel config:

```
Anavel\Crud\CrudModuleProvider::class
```

Copy the package config to your local config with the publish command:

```
php artisan vendor:publish
```

## Configuration

To start CRUDing your models just add them to the config file as follows:

```
...
'models' => [
    'Users'      => App\User::class,
    'Blog Posts' => App\Post::class
...
```

And that's all! TA-DÁ! You will find a full-featured CRUD on you admin panel.

## Documentation

Visit the [wiki](https://github.com/anavel/crud/wiki) for more detailed information on how to customize your configuration based CRUD.

## License

This software is published under the MIT License
