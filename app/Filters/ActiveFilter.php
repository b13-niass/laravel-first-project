<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ActiveFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property)
    {
        if ($value === 'oui') {
            // Filtrer les articles où la quantité est supérieure à 0
            return $query->where($property,  true);
        }
        if ($value === 'non') {
            // Filtrer les articles où la quantité est égale à 0
            return $query->where($property, false);
        }
        // Sinon, vous pouvez choisir de retourner le query tel quel ou filtrer d'une autre manière
        return $query;
    }
}
