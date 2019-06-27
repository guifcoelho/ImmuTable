<?php

namespace guifcoelho\JsonModels\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class FacadeAccessor
 */
class JsonModelsFacadeAccessor extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'jsonmodels.model';
    }
}
