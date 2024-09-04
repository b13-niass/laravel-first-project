<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CarteFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'carte';
    }
}
