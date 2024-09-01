<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class IncludeRoleFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property)
    {
        return $query->whereHas('role', function (Builder $query) use ($value) {
            $query->where('role', $value);
        });
    }
}
