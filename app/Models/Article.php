<?php

namespace App\Models;

use App\Filters\QuantityFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'libelle',
        'prix',
        'qte',
    ];

    /**
     * The attributes that are not mass assignable
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * La relation "plusieurs à plusieurs" avec le modèle Dette.
     */
    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'article_dette')
            ->withPivot('qteVente', 'prixVente')
            ->withTimestamps();
    }

    /**
     * Scope a query to apply custom filters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, $request)
    {
        // Apply the 'disponible' filter using the custom QuantityFilter
        if ($request->has('disponible')) {
            $disponible = $request->query('disponible');
            $query = (new QuantityFilter())($query, $disponible, 'disponible');
        }

        // Use Spatie's QueryBuilder to handle other allowed filters
        $query = QueryBuilder::for($query)
            ->allowedFilters(['libelle']);

        return $query;
    }
}
