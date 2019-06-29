[![Build Status](https://travis-ci.com/guifcoelho/json-models.svg?branch=master)](https://travis-ci.com/guifcoelho/json-models)
![Code Coverage Status](tests/report/coverage.svg)


# JsonModels

Package for using immutable json models instead of regular SQL or NoSQL databases with sintax similar to Laravel Eloquent.

DO NOT use this package if your models' data are likely to change.

# Installation

`composer require guifcoelho/json-models`

# Testing

1. Without Docker
   - Only tests: `./vendor/bin/phpunit`
   - Tests and coverage report: `composer tests-report-[linux|win]`
2. With Docker
   - `cd docker`
   - `bash build`
   - Only tests: `bash phpunit`
   - Tests and coverage report: `bash composer tests-report-[linux|win]`
