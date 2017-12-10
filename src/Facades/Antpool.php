<?php

/**
 * @package aburakovskiy\laravel-antpool-api
 * @author Alexander Burakovskiy <alexander.burakovskiy@gmail.com>
 */
namespace Aburakovskiy\LaravelAntpoolApi\Facades;

use Illuminate\Support\Facades\Facade;

class Antpool extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'antpool';
    }

}