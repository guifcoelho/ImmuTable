[![Build Status](https://travis-ci.com/guifcoelho/ImmuTable.svg?branch=master)](https://travis-ci.com/guifcoelho/ImmuTable)
![Code Coverage Status](tests/report/coverage.svg)


# ImmuTable

Package for using immutable ndjson models instead of regular SQL or NoSQL databases with sintax similar to Laravel Eloquent.

DO NOT use this package if your data are likely to change.

# Installation

`composer require guifcoelho/immu-table`

# How to use it

## Configuration

The config class will look for a `config_path()` function. This function must return the configuration files repository where the `immutable.php` configuration file is located.

Copy `immutable.php` configuration file from `src/Config` and paste it into your own configuration folder.

By default, the tables will be stored in `storage/app/immutable/tables`.

The Engine class will load the tables in chunks of data. You can increase or decrese the `chunk_size` in the configuration file.

## Declaring your model

Create models the same way as in Laravel Eloquent:

```php
use guifcoelho\ImmuTable\Model;

class Sample extends Model
{
    protected $table = "table_example";
}
```

It will load your data from the table `table_example.ndjson` and set all fields accordingly. If you want to restrict the fields to be loaded, just include the protected array `$fields`:

```php
use guifcoelho\ImmuTable\Model;

class Sample extends Model
{
    protected $fields = ['id', 'name', 'email'];
}
```

If you do not want some fields to be returned in the `toArray()` or `toJson()` functions, just include their names in the `$hidden` array:

```php
use guifcoelho\ImmuTable\Model;

class Sample extends Model
{
    protected $hidden = ['this', 'that'];
}
```

If you want your primary key to be anything but 'id', just declare it as below (remember that your primary key must be unique and integer):

```php
use guifcoelho\ImmuTable\Model;

class Sample extends Model
{
    protected $primary_key = 'not_id';
}
```

## Querying your models

You can query your model for data the same way as in Laravel Eloquent:

```php
$query = SampleModel::where('id', 10)->first();
```

or,

```php
$query = SampleModel::where('price', '>', 50)->first();
```

or chaining 'where' clauses, 

```php
$query = SampleModel::where('price', '>', 50)->where('id', '<=', 10)->get();
```

or chaining 'orWhere' clauses

```php
$query = SampleModel::where('price', '>', 50)
            ->where('id', '<=', 10)
            ->orWhere('price', '<', 10)
            ->get();
```

## Declaring relations

You can declare relations between models the same way as Laravel Eloquent. Please, look into the `Model` class to see which relations are implemented.

```php
use guifcoelho\ImmuTable\Model;

use Sample2;
use Sample3;
use Sample4;
use Sample5;

class Sample1 extends Model
{
    protected $table = "table_example";

    public function owner(){
        return $this->belongToOne(Sample2::class [, $field, $field_in_parent_class]);
    }

    public function parents(){
        return $this->belongsToMany(Sample3::class [, $pivot_table, $field_in_pivot, $parent_field_in_pivot, $field, $field_in_parent])
    }

    public function child(){
        return $this->hasOne(Sample4::class [, $field_in_child_model, $field]);
    }

    public function children(){
        return $this->hasMany(Sample5::class [, $field_in_child_models, $field]);
    }
}
```
In the example above, the fields inside brackets are optional. See below a better explanation:

- `belongsToOne`: You must provide the parent class name. If necessary, provide the foreign key name inside the child model, and the related field name inside the parent model.
- `belongsToMany`: You must provide the parent class name. If necessary, provide the pivot table name, the current model's foreign key name in the pivot table, the parent model's foreign key name in the pivot table, the related current model's field name, and the related parent model's field name.
- `hasOne`: You must provide the child class name. If necessary, provide the foreign key name inside the child model and the related field name inside the parent model.
- `hasMany`: You must provide the children class name. If necessary, provide the foreign key name inside the children models and the related field name inside the parent model.


# Contributing and testing

- Only tests: `./vendor/bin/phpunit`
- Tests and coverage report: `composer tests-report-[linux|win]`
