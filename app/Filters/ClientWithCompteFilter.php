<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ClientWithCompteFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property)
    {
        if ($value === 'oui') {
             $query->whereHas('user')->with('user');
        }
        if ($value === 'non'){
             $query->whereDoesntHave('user');
        }
        return $query;
    }
}
