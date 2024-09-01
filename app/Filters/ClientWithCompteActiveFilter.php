<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ClientWithCompteActiveFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property)
    {
        if ($value === 'oui') {
             $query->whereHas('user', function (Builder $query) {
                $query->where('active', true);
            })->with('user');
        }
        if ($value === 'non'){
            $query->whereHas('user', function (Builder $query) {
                $query->where('active', false);
            })->with('user');
        }
        return $query;
    }
}
